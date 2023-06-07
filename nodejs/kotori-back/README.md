# Kotori Project backend

## 简介

使用[Express](https://expressjs.com/)框架，搭配`sqlite`编写的Image Host后端框架，
数据库连接库使用的[better-sqlite3](https://github.com/WiseLibs/better-sqlite3)

## 使用方式

* 进入kotori-back目录（假设当前目录即为本文件所在目录）

  `cd kotori-back`

* 使用`pnpm`安装所需包

  `pnpm i --production`

* 生成数据库（拥有可省略）

  `node db/create_db.js kotori_project.db`

* 配置生产环境变量文件`.env`
  
  `cp .env.example .env`

  修改项目，根据数据库和图片文件存放路径修改`DATABASE_PATH`, `IMAGE_PATH`和`THUMBNAIL_PATH`的值

  例如新建文件夹`uploads`和`thumbnails`

* 启动项目（生产环境）

  `pnpm run serve`

  建议使用例如`pm2`, `forever`这类型进程管理器来启动

## 贡献代码

* 使用`pnpm`安装所需包

  `pnpm i`

* 生成数据库（拥有可省略）

  `node db/create_db.js kotori_project.db`

* 启动开发环境

  `pnpm run dev`