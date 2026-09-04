# FASHION-DEPLOY-2.6-TEST001

## 任务名称
Hostinger 首次受控真实部署验证

## 前置状态

- P2-T004：部署通道建立已通过并合并。
- P2-T005：`dry_run=true` 首次 GitHub Actions 联调已通过并合并。
- 当前正式部署 Workflow：`.github/workflows/deploy-hostinger.yml`。
- 部署范围仍严格限制为：
  - `wordpress/themes/fashion-child/` → `$HOSTINGER_WP_PATH/wp-content/themes/fashion-child/`
  - `wordpress/plugins/fashion-core/` → `$HOSTINGER_WP_PATH/wp-content/plugins/fashion-core/`
- 当前两个源码目录仅包含 `.gitkeep` 占位文件，不包含 PHP、主题、插件或商城业务代码。

## 任务目标

执行第一次 `dry_run=false` 的真实部署，仅验证已经通过 dry-run 的部署通道能否安全地在 Hostinger 上创建/同步两个项目自有占位目录。

本任务不是商城开发任务，不安装主题、不激活主题、不创建插件功能、不修改 WordPress/WooCommerce 配置。

## 分支要求

先同步最新 `main`，再新建：

`codex/fashion-deploy-2.6-live`

不得直接在 `main` 上修改报告或其他文件。

## 执行前检查

1. 确认本地 `main` 与 `origin/main` 同步且工作区 clean。
2. 确认 `.github/workflows/deploy-hostinger.yml` 与已通过 P2-T005 的版本一致，本任务不得为了通过真实部署而修改 Workflow。
3. 确认源码目录中只存在占位文件：
   - `wordpress/themes/fashion-child/.gitkeep`
   - `wordpress/plugins/fashion-core/.gitkeep`
4. 不允许添加 PHP、CSS、JS、主题文件、插件文件或商城业务代码。

## 真实部署执行

从默认分支 `main` 手动触发：

`.github/workflows/deploy-hostinger.yml`

输入必须为：

`dry_run=false`

只允许执行一次首次受控真实部署；如果失败，不得连续重试或通过放宽安全检查来绕过失败。

## 预期 Actions 行为

必须满足：

1. Checkout：success。
2. Configure SSH：success。
3. Hostinger preflight：success。
4. Backup step：执行（不是 skipped）。
   - 如果目标自有目录此前不存在或为空，允许输出“无需备份”。
   - 不得备份整个 WordPress。
5. Dry-run preview step：skipped。
6. Real deploy step：success。
7. rsync 目标仍只能是：
   - `wp-content/themes/fashion-child/`
   - `wp-content/plugins/fashion-core/`
8. 不允许输出 Secret、私钥或密码。

## 部署后只读验收

真实部署成功后，进行只读检查。优先使用本机现有 SSH Key 直接连接 Hostinger；不得读取、打印、复制或提交私钥内容。

只允许执行读取/检查命令，不得手工修复服务器文件。

需要确认：

1. `wp-content/themes/fashion-child/` 已存在。
2. `wp-content/plugins/fashion-core/` 已存在。
3. 两个目录中仅有本次占位内容（预期 `.gitkeep`），没有意外文件。
4. 以下现有目录/文件仍存在：
   - `wp-admin/`
   - `wp-includes/`
   - `wp-config.php`
   - `wp-content/plugins/woocommerce/`
   - `wp-content/plugins/litespeed-cache/`
   - `wp-content/themes/twentytwentyfive/`
5. 不得删除或修改任何第三方主题、第三方插件、WordPress Core。
6. 临时域名首页仍可正常访问；HTTP 响应应为正常 2xx/3xx，不得出现 5xx。

## 失败处理规则

出现以下任一情况，立即停止并标记 `BLOCKED`：

- SSH / Host Key 校验失败。
- Preflight 失败。
- rsync 目标超出白名单。
- Workflow 试图修改父目录 `themes/`、`plugins/`、`wp-content/` 或 `public_html/`。
- WordPress Core / WooCommerce / LiteSpeed / Hostinger 插件出现异常修改。
- 网站出现 5xx。
- Secret 或私钥疑似出现在日志。

失败后禁止：

- 改成 `StrictHostKeyChecking=no`。
- 删除权限检查。
- 改用密码登录。
- 手工复制文件到服务器作为“补救”。
- 修改 Workflow 后直接再跑真实部署。

如需修复，必须另开后续任务。

## 禁止事项

本任务禁止：

- 安装或激活任何正式主题。
- 编写 Child Theme 代码。
- 编写 `fashion-core` 插件代码。
- 修改 WooCommerce 配置或数据库。
- 创建商品、分类、品牌、页面或菜单。
- 修改 `wp-config.php`、`.htaccess`。
- 删除 Twenty Twenty-* 主题。
- 删除 Hostinger / WooCommerce / LiteSpeed 插件。
- 启用 `push` 自动部署。
- 开始 P3。

## 开发过程报告

完成后新增：

`docs/FASHION-DEPLOY-2.6-REPORT001.md`

至少记录：

1. 执行分支。
2. Actions Run 链接 / Run ID。
3. 触发 ref（必须为 `main`）。
4. 输入值（必须为 `dry_run=false`）。
5. 各关键 Step 结果。
6. Backup Step 实际结果。
7. rsync 实际写入计划/结果摘要。
8. 部署后服务器只读检查结果。
9. 临时域名 HTTP 检查结果。
10. 是否发现 Secret 泄漏。
11. 是否触碰白名单外文件。
12. 是否存在 BLOCKED。
13. 下一步建议。

报告中不得记录任何 Secret 值、SSH 私钥、密码或完整敏感认证信息。

## PR 要求

- 只提交本任务报告；除非本任务执行前发现仓库状态不一致，否则不应修改 Workflow 或业务文件。
- 创建 PR 到 `main`。
- 不自行 merge。
- PR 标题建议：`P2-T006: validate first controlled Hostinger live deployment`。

## 验收标准

全部满足才算 PASS：

1. 使用 `main` 上已通过 dry-run 的 Workflow。
2. `dry_run=false`。
3. GitHub Actions conclusion = success。
4. Backup Step 正常执行。
5. Real deploy Step success。
6. 仅真实写入两个项目自有目标目录。
7. 部署后两个目录存在且内容符合预期。
8. WordPress Core、WooCommerce、LiteSpeed、Hostinger 与第三方主题未受影响。
9. 临时域名无 5xx。
10. 无 Secret / 私钥泄漏。
11. 完成 `FASHION-DEPLOY-2.6-REPORT001.md`。
12. 创建 PR 到 `main` 且不自行 merge。

## Codex 最终输出要求

完成后只回复：

- 分支名
- PR 编号/链接
- Actions Run ID/链接
- `dry_run` 值
- Actions 结论
- Backup Step 结果
- Real deploy Step 结果
- 服务器实际新增/修改的项目目录
- 临时域名检查结果
- 是否触碰白名单外文件
- 是否存在 Secret 泄漏
- 是否存在 BLOCKED

不要自行合并 PR，不要开始 P3。
