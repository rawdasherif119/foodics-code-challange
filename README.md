## About Foodics Code Challange App & Main Idea

**Setup**

After Installing, take a copy for .env.example to .env and fill out the system database connection

1 -
```sh
composer install
```

2-
```sh
php artisan migrate
```

3-
```sh
php artisan key:generate
```

4-
```sh
php artisan passport:install
```


7- To have a dummy data, run the seeders:
```sh
php artisan db:seed
```

8- To Run test cases:
```sh
php artisan test
```


9- Register User and login api are found in the postman collection 
Or you can use this user (created through seeder) directly without register (also exist as example in collection )

```sh
customerTest1@gmail.com 
```
```sh
password:12345678:
```


10- Postman Collection: [Postman Collection](https://documenter.getpostman.com/view/6589767/2s83eyrHS3).
***

**Database schema**
In a system that has three main models; Product, Ingredient, and Order.
So Our tables and relations will be :

- ingredients
- products
** relation between them will be (many to many)-> pivot Table (product_ingredient)
- users
- orders
** relation between  users and orders will be (one to many)
* each order contain many products and each product can be in many order so 
** relation between orders and products (many to many)-> pivot Table (order_product)

**Main Idea**
So at first When Order Request reach our system when need 
1- validate data 
2- Store order and order details 
3- Updates the stock of the ingredients.
* We need perform all these steps together or nothing -> best choice here use database Transactions  to rollback if any error happen

* Also updated event observer is used to observe on level of ingredients and if any of ingredient  level reached below 50% observer will catch this event and this send Email 
* to enhance performance this email is queued

* And boolean flag called (alert email sent) is used in database (ingredients table) for send email only one time after any level of ingredients reached below 50% , this flag will mark as true to not send this mail again and when merchants charge there stock again this flag must be return false . 
