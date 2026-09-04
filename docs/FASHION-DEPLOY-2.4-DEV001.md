# FASHION-DEPLOY-2.4-DEV001

## 任务名称
Hostinger WordPress 安全部署通道建立

## 当前环境已确认

- WordPress 已创建并运行于 Hostinger 临时域名环境。
- WooCommerce 已安装并启用。
- Hostinger SSH 已启用。
- SSH Key 免密码登录已验证成功。
- WordPress 根目录：`/home/u592611429/domains/gold-wren-658264.hostingersite.com/public_html`
- `wp-content/themes` 可写。
- `wp-content/plugins` 可写。
- 服务器存在 `rsync`、`git`、WP-CLI、PHP 8.3。
- GitHub Repository Secrets 已建立：
  - `HOSTINGER_SSH_HOST`
  - `HOSTINGER_SSH_PORT`
  - `HOSTINGER_SSH_USER`
  - `HOSTINGER_WP_PATH`
  - `HOSTINGER_SSH_PRIVATE_KEY`

## 任务目标

建立 `fashion` 仓库到 Hostinger WordPress 的安全部署基础，但本任务不开发任何商城业务功能、不替换正式主题、不修改 WooCommerce 配置。

部署范围必须严格限制在本项目自有目录：

- `wp-content/themes/fashion-child/`
- `wp-content/plugins/fashion-core/`

禁止将整个 WordPress、整个 `public_html`、整个 `wp-content`、整个 `themes` 或整个 `plugins` 目录纳入覆盖部署。

## 分支要求

新建开发分支：

`codex/fashion-deploy-2.4`

不得直接在 `main` 上开发。

## 必须建立的仓库结构

```text
fashion/
├── .github/
│   └── workflows/
│       └── deploy-hostinger.yml
├── wordpress/
│   ├── themes/
│   │   └── fashion-child/
│   │       └── .gitkeep
│   └── plugins/
│       └── fashion-core/
│           └── .gitkeep
├── docs/
└── README.md
```

首轮只允许使用占位文件，不得加入实际主题 PHP、插件 PHP 或业务代码。

## GitHub Actions 要求

### 1. 触发方式

首版必须仅支持：

`workflow_dispatch`

禁止先做 `push to main` 自动部署。自动部署需要首轮人工验收通过后再单独启用。

### 2. 权限

Workflow 顶层设置最小权限：

```yaml
permissions:
  contents: read
```

### 3. 并发控制

配置固定 concurrency group，防止两个部署同时执行。

建议：

```yaml
concurrency:
  group: fashion-hostinger-deploy
  cancel-in-progress: false
```

### 4. SSH

必须仅从 GitHub Secrets 读取：

- `HOSTINGER_SSH_HOST`
- `HOSTINGER_SSH_PORT`
- `HOSTINGER_SSH_USER`
- `HOSTINGER_WP_PATH`
- `HOSTINGER_SSH_PRIVATE_KEY`

私钥不得写入仓库、日志、artifact、echo 输出或任何 Markdown 文档。

Workflow 中创建临时私钥文件时必须设置：

```bash
chmod 600
```

### 5. Host Key

必须启用 SSH host key 检查，不允许：

```text
StrictHostKeyChecking=no
```

可以在 job 内通过 `ssh-keyscan` 写入临时 runner 的 `known_hosts`，但不得将私钥或其他 Secret 输出。

### 6. Preflight

真正 rsync 前必须先通过 SSH 执行只读/权限检查：

- `$HOSTINGER_WP_PATH/wp-content` 存在
- `$HOSTINGER_WP_PATH/wp-content/themes` 可写
- `$HOSTINGER_WP_PATH/wp-content/plugins` 可写

任一检查失败，Workflow 必须立即失败，不得继续同步。

### 7. 部署路径白名单

Theme source：

`wordpress/themes/fashion-child/`

Theme destination：

`$HOSTINGER_WP_PATH/wp-content/themes/fashion-child/`

Plugin source：

`wordpress/plugins/fashion-core/`

Plugin destination：

`$HOSTINGER_WP_PATH/wp-content/plugins/fashion-core/`

禁止写成：

```text
wordpress/themes/ -> wp-content/themes/
wordpress/plugins/ -> wp-content/plugins/
```

避免误删第三方主题与插件。

### 8. rsync

仅允许在上述两个自有目标目录内同步。

可以使用 `--delete`，但前提是 `--delete` 的目标只能是：

- `fashion-child/`
- `fashion-core/`

绝对禁止对父目录 `themes/` 或 `plugins/` 使用 `--delete`。

### 9. 首轮 dry-run

Workflow 必须提供一个 `dry_run` 输入，默认值为 `true`。

- `dry_run=true`：使用 rsync `--dry-run`，仅验证 SSH、目标目录和同步计划，不落地修改服务器文件。
- `dry_run=false`：才执行真实 rsync。

本任务开发完成后只运行 `dry_run=true` 做第一次 Actions 验证。

### 10. 备份

真实部署模式下，在覆盖 `fashion-child` 或 `fashion-core` 前，如果目标目录已经存在且非空，先在用户 home 下建立部署备份，例如：

`~/fashion-deploy-backups/<UTC时间戳>/`

只备份本项目自有两个目录，不备份整个 WordPress。

至少保留最近 5 次；本任务可实现简单清理逻辑。

### 11. 禁止事项

本任务中禁止：

- 安装主题。
- 激活主题。
- 修改 WordPress 数据库。
- 修改 WooCommerce 设置。
- 修改 `wp-config.php`。
- 修改 `.htaccess`。
- 修改 Hostinger 插件。
- 修改 LiteSpeed Cache。
- 修改 WooCommerce 插件文件。
- 删除 Twenty Twenty-* 主题。
- 删除任何现有插件。
- 使用 FTP 密码或 SSH 密码写进 Workflow。
- 创建任何业务商品、页面、分类或品牌。
- 让 GitHub 管理 WordPress Core。

## README 更新

完成后在 README 的当前阶段中补充一条简短状态：

- Hostinger WordPress 部署通道已建立，首版为手动 GitHub Actions + SSH/rsync，正式业务代码尚未开始。

不要重写已经确认的产品需求。

## 开发过程报告

完成后新增：

`docs/FASHION-DEPLOY-2.4-REPORT001.md`

至少包含：

1. 实际新增/修改文件。
2. Workflow 触发方式。
3. Secret 名称清单（只写名称，不写值）。
4. 实际部署白名单路径。
5. Dry-run 验证结果。
6. 是否对服务器产生文件修改。
7. 已知风险。
8. 下一步建议。

## 验收标准

本任务只有全部满足以下条件才算 PASS：

1. 开发在 `codex/fashion-deploy-2.4` 分支完成。
2. `.github/workflows/deploy-hostinger.yml` 存在。
3. Workflow 仅 `workflow_dispatch`，无自动 push 部署。
4. Secret 全部通过 `${{ secrets.* }}` 使用，无硬编码 IP、用户、私钥、WordPress 路径。
5. 同步范围严格限制在 `fashion-child` 和 `fashion-core`。
6. 不存在对整个 `public_html`、`wp-content`、`themes`、`plugins` 的覆盖/删除逻辑。
7. 默认 `dry_run=true`。
8. Actions dry-run 能成功建立 SSH 并通过路径权限检查。
9. Dry-run 不修改服务器文件。
10. README 和开发报告完成。
11. 提交 PR 到 `main`，不要自行 merge。

## Codex 最终输出要求

完成后只需要回复：

- 分支名
- PR 编号/链接
- Workflow 文件路径
- Dry-run Actions 结果
- 变更文件清单
- 是否触碰服务器文件（预期：否）
- 是否存在 BLOCKED

不要自行合并 PR，不要开始下一阶段。
