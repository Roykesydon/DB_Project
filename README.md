# DB_Project

## Config
    - docker-compose.yml
        environment:
          MARIADB_ROOT_PASSWORD: 自己填 -> 密碼
          MARIADB_DATABASE: db_final -> 資料庫名稱
          MARIADB_USER: 自己填 -> 帳戶
          MARIADB_PASSWORD: 自己填 -> 密碼
        
    - config.yml("/backend/initDB/config.yml")
        db:
            host : localhost
            user : 自己填(與上面一樣)
            password : 自己填(與上面一樣)
            database : db_final

    - config.js("/frontend/RentHub/public/config.js")
        export const googleMapAPI = "填入你申請的 API 金鑰";
        需要至少開啟三項服務
            - Geocoding API
            - Maps JavaScript API
            - Places API

## Frontend
```
npm install
npm run serve (開發用)
```

## Backend
```
in DB_Project開啟服務(需先填完所有config):
docker-compose up

初始化資料庫(需先啟動服務):
cd backend/initDB/
pipenv install
pipenv run python initDB.py

記得IP需連到Docker Container內的IP，使用以下指令查詢查詢docker container IP：
docker inspect container_ID | grep "IPAddress"
```

## Port
 - 8000 : API
 - 8001 : PhpMyAdmin