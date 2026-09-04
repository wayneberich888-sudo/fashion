(function () {
  'use strict';

  function formatRemaining(milliseconds) {
    var seconds = Math.max(0, Math.floor(milliseconds / 1000));
    var days = Math.floor(seconds / 86400);
    var hours = Math.floor((seconds % 86400) / 3600);
    var minutes = Math.floor((seconds % 3600) / 60);
    var remainder = seconds % 60;
    var clock = [hours, minutes, remainder]
      .map(function (value) { return String(value).padStart(2, '0'); })
      .join(':');
    return days > 0 ? days + '일 ' + clock : clock;
  }

  function updateCountdowns() {
    document.querySelectorAll('[data-sale-end]').forEach(function (clock) {
      var remaining = Number(clock.dataset.saleEnd) - Date.now();
      var value = clock.querySelector('.fashion-sale-clock__value');
      if (!value) {
        return;
      }
      if (remaining <= 0) {
        clock.hidden = true;
        return;
      }
      value.textContent = formatRemaining(remaining);
    });
  }

  function fallbackCopy(text) {
    var textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.setAttribute('readonly', '');
    textarea.style.position = 'fixed';
    textarea.style.opacity = '0';
    document.body.appendChild(textarea);
    textarea.select();
    var copied = document.execCommand('copy');
    textarea.remove();
    return copied ? Promise.resolve() : Promise.reject(new Error('copy failed'));
  }

  function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
      return navigator.clipboard.writeText(text);
    }
    return fallbackCopy(text);
  }

  function setStatus(container, message, state) {
    var status = container.querySelector('.fashion-support-status');
    if (!status) {
      return;
    }
    status.textContent = message;
    status.dataset.state = state;
  }

  document.addEventListener('click', function (event) {
    var consultation = event.target.closest('[data-kakao-consult]');
    var copyButton = event.target.closest('[data-copy-product-link]');
    var control = consultation || copyButton;
    if (!control) {
      return;
    }

    var container = control.closest('.fashion-product-support');
    if (!container) {
      return;
    }

    var sku = container.dataset.productSku || '';
    var url = container.dataset.productUrl || window.location.href;
    var payload = consultation
      ? '카카오 상품 상담 · SKU ' + sku + '\n' + url
      : url;

    copyText(payload).then(function () {
      if (consultation) {
        container.dataset.consultationPayload = payload;
        setStatus(container, '카카오 상담 정보가 준비되었습니다 · ' + sku, 'success');
      } else {
        setStatus(container, '현재 상품 링크를 복사했습니다.', 'success');
      }
    }).catch(function () {
      setStatus(container, '복사하지 못했습니다. 다시 시도해 주세요.', 'error');
    });
  });

  updateCountdowns();
  window.setInterval(updateCountdowns, 1000);
}());
