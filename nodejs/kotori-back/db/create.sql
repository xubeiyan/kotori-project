CREATE TABLE "images" (
	"id"	INTEGER UNIQUE,
	"filename"	TEXT NOT NULL,
	"filetype"	TEXT NOT NULL,
	"filesize"	INTEGER NOT NULL,
	"upload_time"	TEXT NOT NULL,
	"uploader_id"	INTEGER NOT NULL,
	PRIMARY KEY("id" AUTOINCREMENT)
);

CREATE TABLE "users" (
	"id"	INTEGER NOT NULL,
	"username"	TEXT NOT NULL,
	"password"	TEXT NOT NULL,
	"password_salt"	TEXT NOT NULL,
	"status"	TEXT NOT NULL DEFAULT 'enable',
	PRIMARY KEY("id" AUTOINCREMENT)
);