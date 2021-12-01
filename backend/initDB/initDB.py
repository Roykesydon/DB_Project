import pymysql
import yaml


with open('config.yml', 'r') as f:
    cfg = yaml.safe_load(f)

connection = pymysql.connect(host=cfg['db']['host'],user=cfg['db']['user'],password=cfg['db']['password'],db=cfg['db']['database'],charset='utf8mb4')

cursor = connection.cursor()
cursor.execute("DROP TABLE IF EXISTS rentRoom;")
connection.commit()

cursor.execute("DROP TABLE IF EXISTS roomPicture;")
connection.commit()

cursor.execute("DROP TABLE IF EXISTS user;")
connection.commit()

cursor.execute("DROP TABLE IF EXISTS roomService;")
connection.commit()

cursor.execute("DROP TABLE IF EXISTS roomQueue;")
connection.commit()

cursor.execute(
    "CREATE TABLE IF NOT EXISTS user( \
        user_ID varchar(50) NOT NULL, \
        user_name  varchar(30) NOT NULL, \
        password varchar(70) NOT NULL, \
        email varchar(50) NOT NULL, \
        phone_number varchar(25) NOT NULL, \
        access_key varchar(100), \
        timestamp varchar(50), \
        PRIMARY KEY (user_ID) \
    );")
connection.commit()

cursor.execute(
    "CREATE TABLE IF NOT EXISTS rentRoom( \
        room_ID int(8) NOT NULL AUTO_INCREMENT, \
        user_ID varchar(50) NOT NULL, \
        room_name  varchar(25) NOT NULL, \
        address varchar(50) NOT NULL, \
        cost varchar(7) NOT NULL, \
        room_info varchar(300), \
        room_latitude numeric(8,5) NOT NULL, \
        room_longitude numeric(8,5) NOT NULL, \
        room_city varchar(20) NOT NULL, \
        post_date varchar(30), \
        live_number varchar(3), \
        PRIMARY KEY (room_ID), \
        FOREIGN KEY (user_ID) REFERENCES user(user_ID) \
    );")
connection.commit()

cursor.execute(
    "CREATE TABLE IF NOT EXISTS roomPicture( \
        room_ID int(8) NOT NULL, \
        pictureURL_one varchar(300), \
        pictureURL_two varchar(300), \
        pictureURL_three varchar(300), \
        pictureURL_four varchar(300), \
        pictureURL_five varchar(300), \
        pictureURL_six varchar(300), \
        pictureURL_seven varchar(300), \
        pictureURL_eight varchar(300), \
        PRIMARY KEY (room_ID) \
    );")
connection.commit()

cursor.execute(
    "CREATE TABLE IF NOT EXISTS roomService( \
        room_ID int(8) NOT NULL, \
        wifi boolean NOT NULL, \
        internet boolean NOT NULL, \
        tv boolean NOT NULL, \
        refrigerator boolean NOT NULL, \
        parking boolean NOT NULL, \
        ac boolean NOT NULL, \
        washing_machine boolean NOT NULL, \
        can_cooking boolean NOT NULL, \
        can_keep_pet boolean NOT NULL, \
        PRIMARY KEY (room_ID) \
    );")
connection.commit()

cursor.execute(
    "CREATE TABLE IF NOT EXISTS roomQueue( \
        room_ID int(5) NOT NULL, \
        user_ID_one varchar(50), \
        user_ID_two varchar(50), \
        user_ID_three varchar(50), \
        user_ID_four varchar(50), \
        user_ID_five varchar(50), \
        user_ID_six varchar(50), \
        user_ID_seven varchar(50), \
        user_ID_eight varchar(50), \
        user_ID_nine varchar(50), \
        user_ID_ten varchar(50), \
        PRIMARY KEY (room_ID) \
    );")
connection.commit()