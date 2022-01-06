# Database End of Term Project



## For development

### Frontend
```
cd ./frontend/RentHub
npm install
npm run serve
```

---

## Execute project

### 1. Write config
    - .env
        MARIADB_ROOT_PASSWORD=填寫root的密碼
        
    - config.yml("/backend/initDB/config.yml")
        db:
            host : localhost
            user : root
            password : "填寫root的密碼"
            database : db_final

    - config.js("/frontend/RentHub/public/config.js")
        export const googleMapAPI = "填入你申請的 API 金鑰";
        需要至少開啟下列三項服務
            - Geocoding API
            - Maps JavaScript API
            - Places API

### 2. Run project with docker-compose
```
in DB_Project開啟服務(需先填完所有config):
docker-compose up
```

### 3. Init database (Only needed for the first execution)
```
初始化資料庫(需先啟動服務):
cd backend/initDB/
pipenv install
pipenv run python initDB.py
```

## Add testing data

The button "新增測試資料" in the home page will insert below items to database:
- 5 account.
- 1 room for every city with every created account above.


## Port
 - 3306 : Database(This port is for initializing database)
 - 8000 : API
 - 8001 : PhpMyAdmin
 - 8005 : App
