# FASHION-DEPLOY-2.4-REPORT001

## 1. 实际新增/修改文件

- 新增 `.github/workflows/deploy-hostinger.yml`
- 新增 `wordpress/themes/fashion-child/.gitkeep`
- 新增 `wordpress/plugins/fashion-core/.gitkeep`
- 修改 `README.md`
- 新增 `docs/FASHION-DEPLOY-2.4-REPORT001.md`

首轮仅建立部署基础和占位目录，没有加入主题 PHP、插件 PHP 或商城业务代码。

## 2. Workflow 触发方式

- 仅支持 `workflow_dispatch` 手动触发。
- 输入 `dry_run` 为必填 boolean，默认值为 `true`。
- 未配置 `push`、`pull_request`、定时任务或其他自动部署触发器。
- 固定 concurrency group 为 `fashion-hostinger-deploy`，`cancel-in-progress: false`。
- 顶层权限为 `contents: read`。

## 3. Secret 名称清单

- `HOSTINGER_SSH_HOST`
- `HOSTINGER_SSH_PORT`
- `HOSTINGER_SSH_USER`
- `HOSTINGER_WP_PATH`
- `HOSTINGER_SSH_PRIVATE_KEY`

仓库和本报告中未记录任何 Secret 值。

## 4. 实际部署白名单路径

| 类型 | Source | Destination |
| --- | --- | --- |
| Theme | `wordpress/themes/fashion-child/` | `$HOSTINGER_WP_PATH/wp-content/themes/fashion-child/` |
| Plugin | `wordpress/plugins/fashion-core/` | `$HOSTINGER_WP_PATH/wp-content/plugins/fashion-core/` |

`--delete` 仅用于以上两个项目自有目标目录，没有对 `public_html`、`wp-content`、`themes` 或 `plugins` 父目录执行覆盖或删除。

## 5. Dry-run 验证结果

- 本地 Workflow 契约检查：PASS。
  - YAML 可解析。
  - 6 个 shell step 均通过 `bash -n`。
  - 已确认仅有 `workflow_dispatch`、默认 `dry_run=true`、最小权限、严格 Host Key 检查和精确白名单路径。
- GitHub Actions 首次 `dry_run=true` 调度：BLOCKED，未产生 Actions run。
  - GitHub API 返回：`HTTP 404: workflow deploy-hostinger.yml not found on the default branch`。
  - 原因：这是新建 Workflow，尚未存在于默认分支；GitHub 只允许对默认分支已登记的 `workflow_dispatch` Workflow 发起手动调度。
  - 未通过 merge、自动触发器或其他方式绕过该限制。

## 6. 是否对服务器产生文件修改

否。GitHub Actions 没有生成 run，未建立 Hostinger SSH 会话，未执行 rsync，因此未修改服务器文件。

## 7. 已知风险

1. 首次 `workflow_dispatch` 必须等 Workflow 由授权方合并到默认分支后才能执行；当前任务禁止自行 merge，因此真实 Actions dry-run 证据暂缺。
2. `ssh-keyscan` 满足严格 Host Key 检查，但首次采集仍属于 TOFU；后续可在独立任务中改为校验预先确认的 Host Key 指纹。
3. 真实部署会在覆盖非空自有目录前备份至 `~/fashion-deploy-backups/<UTC时间戳>/`。当前保留全部备份，满足至少保留最近 5 次，但需要后续关注空间增长。

## 8. 下一步建议

1. 等待 GPT 独立验收本 PR，不自行 merge。
2. 验收通过并由授权方合并到 `main` 后，首次手动运行必须保持 `dry_run=true`。
3. 检查该 run 的 SSH、路径权限和两条 rsync 计划；在获得后续明确授权前，不运行 `dry_run=false`，不开始下一阶段。
