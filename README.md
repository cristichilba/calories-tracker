# Calories & Macro tracker

### Features
- track food you eat on a per day basis
- calculate total calories and macros per meal and per day
- search products by name / barcode / QR code(?) 
- register foods if they don't already exist in the database
- see your weekly/monthly stats overview (fancy graphs?)
- export your daily summary (pdf? excel?)
- display a pie chart of calorie breakdown percentages (x% fat, y% carbs, z% protein)

### Data structure

##### Product
- `id`  -> *int, auto increment, PK*
- `name` *varchar* -> *varchar, index*
- `quantity` in grams -> *int*
- `calories` per 100g -> *int*
- `carbs` per 100g -> *int*
- `protein` per 100g -> *int*
- `fat` per 100g -> *int*

##### Recipe -> more products can saved grouped into a recipe
- `id` -> *int, auto increment, PK*
- `name` -> *varchar*
- `dateCreated` -> *date*
- `dateUpdated` -> *date*

##### Meal -> products and/or recipes can be added to a meal 
- `id` -> *int, auto increment, PK*
- `userId` -> *int, FK*
- `type` -> *enum 0-3 (snacks, breakfast, lunch, dinner)*
- `date` -> *date*

##### User/Admin

##### Data relationship 
- **product-recipe**: many to many
- **product-meal**: many to many
- **recipe-meal**: many to many
- **user-meal**: one to many
- **user-recipe**: one to many