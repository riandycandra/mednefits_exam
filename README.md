# Mednefits Backend Practical Exam

## Config the app

 1. Rename `.env.example` to `.env` and set DB configuration
 2. Run command in root project to install vendor `composer install`
 3. Run command in root project to migrate the database `php artisan migrate --seed`

## End Point
**[+] Retrieve Role**

|Method| URL | Param |
|--|--|--|
| GET | /role | null |

**[+] Retrieve User**
|Method| URL | Param |
|--|--|--|
| GET | /user| null |

**[+] Retrieve Clinic**
|Method| URL | Param |
|--|--|--|
| GET | /clinic| null |

**[+] Retrieve Booking**
|Method| URL | Param |
|--|--|--|
| GET | /booking| null |



**1. Register User**
|Method| URL | Param |
|--|--|--|
| POST| /user/register| username, password, role_id|

**2. Register Clinic**
|Method| URL | Param |
|--|--|--|
| POST| /clinic/register| name|

**3. Assign User Role**
|Method| URL | Param |
|--|--|--|
| POST| /user/assign| user, role|

**4. Create New Booking**
|Method| URL | Param |
|--|--|--|
| POST| /booking/start| user, clinic|

**5. End Current Active Booking**
|Method| URL | Param |
|--|--|--|
| POST| /booking/end| user|
