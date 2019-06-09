import sqlite3, os, sys

db_file = './kotori.db'

if not os.path.isfile(db_file):
	print('database file not exists...')
	exit()
	
conn = sqlite3.connect(db_file)
	
if len(sys.argv) > 1 and sys.argv[1] == 'drop':
	conn.execute('''DROP TABLE IF EXISTS image''')
	conn.execute('''DROP TABLE IF EXISTS user''')
	print('drop image and user table...')
	exit()

conn.execute('''CREATE TABLE IF NOT EXISTS image (
	id INTEGER PRIMARY KEY,
	filename CHAR, 
	filetype CHAR, 
	uploader INTEGER, 
	uploadtime DATETIME, 
	limits INTEGER
	)''')
	
conn.execute('''CREATE TABLE IF NOT EXISTS user (
	id INTEGER PRIMARY KEY,
	username CHAR,
	password CHAR,
	lastuploadid INTEGER,
	lastuploadtime DATETIME,
	authority INTERGER
	)''')
	
print('create image and user table to ' + db_file +'...')