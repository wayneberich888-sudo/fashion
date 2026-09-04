#!/usr/bin/env python3
"""Browser acceptance and evidence capture for the local Botiga prototype."""

from __future__ import annotations

import argparse
import math
import os
import re
from pathlib import Path
from urllib.parse import urlsplit, urlunsplit

from playwright.sync_api import BrowserContext, Page, sync_playwright


BASE_URL = os.environ.get("FASHION_POC_URL", "http://127.0.0.1:8097").rstrip("/")
PRODUCT_PATH = "/product/fpoc-001-%ed%81%b4%eb%9d%bc%ec%9a%b0%eb%93%9c-%eb%9f%ac%eb%84%88-%ec%8a%a4%ed%86%a4/"
PAGES = {
    "home": "/",
    "shop": "/shop/",
    "category": "/product-category/shoes/",
    "product": PRODUCT_PATH,
}
REPO_ROOT = Path(__file__).resolve().parents[3]
EVIDENCE_DIR = REPO_ROOT / "docs" / "evidence" / "p3-t001-alt001"
FORBIDDEN_TRANSACTION_TEXT = re.compile(
    r"장바구니|결제|내 계정|Cart|Checkout|My Account|Add to cart", re.IGNORECASE
)


def canonical_url(url: str) -> str:
    """Normalize only the empty query/fragment and trailing slash."""
    parts = urlsplit(url)
    path = parts.path if parts.path.endswith("/") else parts.path + "/"
    return urlunsplit((parts.scheme, parts.netloc, path, "", ""))


def chrome_executable() -> str:
    """Use an already installed browser; never download a system browser."""
    candidates = [
        os.environ.get("PLAYWRIGHT_CHROMIUM_EXECUTABLE", ""),
        "/Applications/Google Chrome.app/Contents/MacOS/Google Chrome",
        "/Applications/Chromium.app/Contents/MacOS/Chromium",
        "/Applications/Microsoft Edge.app/Contents/MacOS/Microsoft Edge",
    ]
    for candidate in candidates:
        if candidate and Path(candidate).is_file():
            return candidate
    raise RuntimeError("No existing supported Chrome-family executable was found.")


def attach_failure_guards(page: Page) -> list[str]:
    """Collect browser/runtime failures for each viewport."""
    failures: list[str] = []

    def on_console(message) -> None:
        if message.type == "error":
            failures.append(f"console.error: {message.text}")

    def on_request_failed(request) -> None:
        if request.resource_type == "document":
            failures.append(f"document request failed: {request.url}")

    def on_response(response) -> None:
        if response.status >= 500:
            failures.append(f"HTTP {response.status}: {response.url}")

    page.on("console", on_console)
    page.on("pageerror", lambda error: failures.append(f"pageerror: {error}"))
    page.on("requestfailed", on_request_failed)
    page.on("response", on_response)
    return failures


def goto(page: Page, path: str) -> None:
    response = page.goto(BASE_URL + path, wait_until="networkidle")
    assert response is not None and response.status < 500, f"unhealthy document: {path}"
    assert page.locator("html").get_attribute("lang", timeout=5_000).startswith("ko")
    assert page.evaluate("document.documentElement.scrollWidth <= window.innerWidth"), (
        f"horizontal overflow at {page.viewport_size}: {path}"
    )
    forbidden = page.locator("a, button").filter(has_text=FORBIDDEN_TRANSACTION_TEXT)
    assert forbidden.count() == 0, f"transaction affordance visible at {path}"


def assert_touch_targets(page: Page) -> None:
    selectors = (
        ".header-search, .menu-toggle, .fashion-bottom-nav a, "
        "[data-kakao-consult], [data-copy-product-link], .woocommerce-ordering select"
    )
    boxes = page.locator(selectors).evaluate_all(
        """elements => elements.filter(element => {
          const style = getComputedStyle(element);
          const rect = element.getBoundingClientRect();
          return style.visibility !== 'hidden' && style.display !== 'none' && rect.width && rect.height;
        }).map(element => {
          const rect = element.getBoundingClientRect();
          return {label: element.textContent.trim() || element.getAttribute('aria-label') || element.className,
                  width: rect.width, height: rect.height};
        })"""
    )
    assert boxes, "no interactive controls were measurable"
    undersized = [box for box in boxes if box["width"] < 44 or box["height"] < 44]
    assert not undersized, f"touch targets below 44px: {undersized}"


def assert_product_name_clamp(page: Page) -> None:
    measurements = page.locator(".woocommerce-loop-product__title").evaluate_all(
        """elements => elements.map(element => {
          const style = getComputedStyle(element);
          return {height: element.getBoundingClientRect().height,
                  lineHeight: parseFloat(style.lineHeight)};
        })"""
    )
    assert measurements, "archive product titles are missing"
    assert all(item["height"] <= item["lineHeight"] * 2 + 1 for item in measurements), (
        f"product title exceeds two lines: {measurements}"
    )


def assert_bottom_nav_clear(page: Page) -> None:
    page.evaluate("window.scrollTo(0, document.documentElement.scrollHeight)")
    page.wait_for_timeout(100)
    overlap = page.evaluate(
        """() => {
          const nav = document.querySelector('.fashion-bottom-nav');
          const last = document.querySelector('.fashion-review');
          if (!nav || getComputedStyle(nav).display === 'none' || !last) return 0;
          const navRect = nav.getBoundingClientRect();
          const lastRect = last.getBoundingClientRect();
          return Math.max(0, Math.min(navRect.bottom, lastRect.bottom) - Math.max(navRect.top, lastRect.top));
        }"""
    )
    assert overlap == 0, f"bottom navigation overlaps the final home section by {overlap}px"


def assert_product_interactions(page: Page, context: BrowserContext) -> None:
    support = page.locator('[data-product-sku="FPOC-001"]')
    assert support.is_visible()
    assert support.get_attribute("data-product-url") == canonical_url(page.url)

    countdown = page.locator("[data-sale-end]")
    assert countdown.is_visible()
    sale_end = int(countdown.get_attribute("data-sale-end"))
    assert sale_end > math.floor(page.evaluate("Date.now()"))
    first_countdown = countdown.locator(".fashion-sale-clock__value").inner_text()
    assert re.fullmatch(r"(?:\d+일 )?\d{2}:\d{2}:\d{2}", first_countdown)
    page.wait_for_timeout(1_100)
    second_countdown = countdown.locator(".fashion-sale-clock__value").inner_text()
    assert second_countdown != first_countdown, "countdown did not advance"

    context.grant_permissions(["clipboard-read", "clipboard-write"], origin=BASE_URL)
    page.locator("[data-kakao-consult]").click()
    status = page.locator(".fashion-support-status")
    page.wait_for_function(
        "document.querySelector('.fashion-support-status').textContent.trim().length > 0"
    )
    status_text = status.inner_text()
    assert "FPOC-001" in status_text and canonical_url(page.url) in status_text, (
        f"unexpected Kakao status {status_text!r}; expected {canonical_url(page.url)!r}"
    )
    payload = support.get_attribute("data-consultation-payload")
    assert payload is not None and "FPOC-001" in payload and canonical_url(page.url) in payload

    page.locator("[data-copy-product-link]").click()
    page.wait_for_timeout(100)
    assert canonical_url(page.evaluate("navigator.clipboard.readText()")) == canonical_url(page.url)


def capture(page: Page, name: str) -> None:
    EVIDENCE_DIR.mkdir(parents=True, exist_ok=True)
    page.evaluate("window.scrollTo(0, 0)")
    page.locator("img").evaluate_all(
        """images => Promise.all(images.map(image => {
          image.loading = 'eager';
          if (image.complete) return Promise.resolve();
          return new Promise(resolve => { image.addEventListener('load', resolve, {once: true});
                                          image.addEventListener('error', resolve, {once: true}); });
        }))"""
    )
    page.screenshot(
        path=str(EVIDENCE_DIR / name),
        full_page=True,
        animations="disabled",
    )


def run_viewport(context: BrowserContext, width: int, height: int, capture_mode: bool) -> None:
    page = context.new_page()
    page.set_viewport_size({"width": width, "height": height})
    failures = attach_failure_guards(page)

    for key in ("home", "shop", "category", "product"):
        goto(page, PAGES[key])
        assert_touch_targets(page)
        if key in ("shop", "category"):
            assert_product_name_clamp(page)
        if key == "product":
            assert page.locator(".woocommerce-product-gallery__image").count() >= 3
            assert page.locator("#tab-title-reviews").is_visible()
            assert page.locator(".related.products").is_visible()

    goto(page, PAGES["product"])
    assert_product_interactions(page, context)

    if width < 768:
        goto(page, PAGES["home"])
        assert_bottom_nav_clear(page)

    if capture_mode:
        capture_jobs = []
        if width == 360:
            capture_jobs = [("home", "mobile-home-360.png")]
        elif width == 390:
            capture_jobs = [
                ("home", "mobile-home-390.png"),
                ("shop", "mobile-shop-390.png"),
                ("product", "mobile-product-390.png"),
            ]
        elif width == 430:
            capture_jobs = [("product", "mobile-product-430.png")]
        elif width == 1440:
            capture_jobs = [("home", "desktop-home-1440.png")]

        for page_key, filename in capture_jobs:
            goto(page, PAGES[page_key])
            capture(page, filename)

    assert not failures, f"browser/runtime failures at {width}px: {failures}"
    page.close()


def main() -> None:
    parser = argparse.ArgumentParser()
    parser.add_argument("--capture", action="store_true", help="write committed PNG evidence")
    args = parser.parse_args()

    with sync_playwright() as playwright:
        browser = playwright.chromium.launch(executable_path=chrome_executable(), headless=True)
        context = browser.new_context(locale="ko-KR", reduced_motion="reduce")
        for width, height in ((360, 800), (390, 844), (430, 932), (1440, 1000)):
            run_viewport(context, width, height, args.capture)
        context.close()
        browser.close()

    print("BROWSER_ACCEPTANCE_PASS viewports=360,390,430,1440")


if __name__ == "__main__":
    main()
