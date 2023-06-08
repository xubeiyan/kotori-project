# Kotori Project frontend

## 简介

使用 `React` 框架，编写的Image Host服务（也就是图床）

## 贡献代码

`NodeJS`包管理器使用的是 [pnpm](https://pnpm.io/)，可自行替换为你喜欢的，例如 `npm` , `yarn` 等

* 进入 `kotori-front` 目录（假设当前目录即为本文件所在目录）

  ```bash
  cd kotori-front
  ```

* 使用 `pnpm` 安装环境

  ```bash
  pnpm i
  ```

* 启动项目（开发）

  ```bash
  pnpm run dev
  ```

## 使用方式

* 进入 `kotori-front` 目录

  ```bash
  cd kotori-front
  ```

* 安装环境（可跳过开发环境）

  ```bash
  pnpm i --prod
  ```

* 打包

  ```bash
  pnpm run build
  ```

* 复制文件

  ```bash
  cp -r dist/ /path/you/want
  ```

* 参照 `vite.config.js` 中的 `proxy` 转发写好 `nginx` 或者 `apache2` 的分流规则
  
  有三种请求需要转发：

  * `/api` 访问后端的API接口 
  * `/images` 访问图片（或者直接在前端处理）
  * `/thumbs` 访问图片略缩图（或者直接在前端处理）


* 由于使用了 `React-Router` 中的 `BroswerRouter`，所以有些网页刷新时会遇到404错误，`nginx` 处理详见[这里](https://stackoverflow.com/questions/45598779/react-router-browserrouter-leads-to-404-not-found-nginx-error-when-going-to)，`apache2` 处理详见[这里](https://stackoverflow.com/questions/44038456/how-to-setup-apache-server-for-react-route)
