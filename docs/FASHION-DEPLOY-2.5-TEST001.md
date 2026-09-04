# FASHION-DEPLOY-2.5-TEST001

## 任务名称
P2-T005：Hostinger 部署通道首次 GitHub Actions Dry-run 联调验证

## 前置状态

- P2-T004 已完成独立验收并已合并到 `main`。
- `.github/workflows/deploy-hostinger.yml` 已存在于默认分支。
- Workflow 仅支持 `workflow_dispatch`，输入 `dry_run` 默认值为 `true`。
- GitHub Repository Secrets 已配置：
  - `HOSTINGER_SSH_HOST`
  - `HOSTINGER_SSH_PORT`
  - `HOSTINGER_SSH_USER`
  - `HOSTINGER_WP_PATH`
  - `HOSTINGER_SSH_PRIVATE_KEY`
- P2-T004 首次 Actions 调度曾因 Workflow 尚未位于默认分支而被 GitHub 阻止；现在该前置条件已经解除。

## 任务目标

只验证现有部署通道能否从 GitHub Actions 成功完成：

`GitHub Actions -> SSH -> Hostinger 路径预检 -> 两个项目自有目录 rsync dry-run`

本任务是验证任务，不是部署任务，不允许产生服务器文件修改，不允许开发商城业务功能。

## 执行分支

本任务的验证对象是 `main` 上已经合并的 Workflow。

如需提交验证报告，新建分支：

`codex/fashion-deploy-2.5-dryrun`

禁止为了让测试通过而直接修改 `main`。

## 必须执行

### 1. 同步本地仓库

开始前：

- `git fetch origin`
- 确认本地 `main` 与 `origin/main` 一致
- 确认工作区 clean

### 2. 触发 GitHub Actions

必须对默认分支 `main` 的：

`.github/workflows/deploy-hostinger.yml`

手动触发一次：

`dry_run=true`

优先使用 GitHub CLI；如本地 `gh` 未登录、权限不足或无法触发，可明确标记为人工操作点，不得改成 `dry_run=false`，不得通过增加自动触发器绕过。

允许的等价命令示例：

```bash
gh workflow run deploy-hostinger.yml --ref main -f dry_run=true
```

### 3. 等待并检查本次 Run

必须取得并记录：

- Actions Run ID
- Actions Run URL
- 运行分支/ref：必须是 `main`
- 输入：必须是 `dry_run=true`
- 最终结论：success / failure

### 4. 必须核对的 Step

以下 Step 预期必须成功：

1. `Check out repository`
2. `Configure SSH`
3. `Preflight Hostinger paths`
4. `Preview project directory sync`

以下 Step 在 dry-run 模式下必须跳过：

1. `Back up existing project directories`
2. `Deploy project directories`

### 5. 日志安全检查

检查 Actions 日志，确认：

- 没有输出 SSH 私钥正文。
- 没有输出任何 GitHub Secret 的完整敏感值。
- 使用严格 Host Key 检查。
- Preflight 通过。
- rsync 输出明确是 `--dry-run` 计划。
- 日志出现 `Dry run completed; no server files were changed.` 或等价成功证据。

### 6. 同步范围检查

Dry-run 的 rsync 目标只能是：

- `$HOSTINGER_WP_PATH/wp-content/themes/fashion-child/`
- `$HOSTINGER_WP_PATH/wp-content/plugins/fashion-core/`

不得出现对以下父级目录的同步或删除计划：

- `public_html/`
- `wp-content/`
- `wp-content/themes/`
- `wp-content/plugins/`

### 7. 禁止事项

本任务禁止：

- `dry_run=false`
- 真实 rsync 部署
- 安装或激活主题
- 安装或修改插件
- 修改 WordPress 数据库
- 修改 WooCommerce 配置
- 修改 `wp-config.php`
- 修改 `.htaccess`
- 修改 Hostinger / LiteSpeed / WooCommerce 文件
- 创建商品、页面、分类、品牌
- 修改 Workflow 以绕过失败
- 新增 `push` 自动部署触发
- 开始 P3 主题/前台开发

## 失败处理规则

如果 dry-run 失败：

1. 先定位失败阶段：GitHub 权限 / Secret / SSH / Host Key / 路径权限 / rsync / Workflow 逻辑。
2. 只记录证据和最小修复建议。
3. 不在本任务中自行扩大范围修复。
4. 标记 `BLOCKED`，等待 GPT 独立审查后再下发修复任务。

## 验证报告

成功或失败都新增：

`docs/FASHION-DEPLOY-2.5-REPORT001.md`

至少包含：

1. Run ID 与 URL。
2. 触发 ref 与 `dry_run` 输入。
3. 各 Step 实际结果。
4. SSH / Preflight 结果。
5. 两条 rsync dry-run 摘要。
6. 备份 Step 是否跳过。
7. 真实 Deploy Step 是否跳过。
8. 是否发现 Secret 泄露。
9. 是否产生服务器文件修改（预期：否）。
10. 最终 PASS / BLOCKED。
11. 下一步建议。

## PR 要求

报告完成后：

- 在 `codex/fashion-deploy-2.5-dryrun` 分支提交报告。
- 创建 PR 到 `main`。
- 不自行 merge。
- PR 不得包含业务代码或无关改动。

## 验收标准

只有全部满足才算 PASS：

1. Workflow 从 `main` 以 `dry_run=true` 成功触发。
2. Actions Run 最终状态为 success。
3. SSH 配置成功。
4. Hostinger 路径 Preflight 成功。
5. Theme dry-run 成功。
6. Plugin dry-run 成功。
7. Backup Step 被跳过。
8. Deploy Step 被跳过。
9. 同步目标严格限于 `fashion-child` 与 `fashion-core`。
10. 无 Secret/私钥泄露。
11. 无服务器文件修改。
12. 验证报告完成并提交 PR。
13. 不自行 merge，不开始下一阶段。

## Codex 最终输出要求

完成后只回复：

- 分支名
- PR 编号/链接
- Actions Run ID/链接
- Dry-run 最终结果
- Preflight 结果
- Theme rsync dry-run 结果
- Plugin rsync dry-run 结果
- Backup Step 是否跳过
- Deploy Step 是否跳过
- 是否触碰服务器文件（预期：否）
- 是否发现 Secret 泄露（预期：否）
- 是否存在 BLOCKED

不要自行合并 PR，不要开始下一阶段。
