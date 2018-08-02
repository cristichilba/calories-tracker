# calories-tracker

DotKernel 3 powered web application for daily calories and macronutrients management

![User Interface](/public/ui.png)

## Setting up

Follow the [DotKernel 3 documentation](https://docs.dotkernel.com/Getting-Started/Installing-DotKernel-3-Frontend.html), with the mention that instead of:

``
 composer create-project dotkernel/frontend -s dev dk3
 ``

You simply have to run the following command: 
`` 
composer install
`` 

## Features

* Track food you eat on a per day basis
* Calculate total calories and macros per meal and per day
* Group products into recipes
* Search products and recipes by name
* Submit new products, which, if they pass admin validation, will be included in the application

## TODO

* [ ] Historical data (weekly/monthly statistics)
* [ ] Fancy pie charts of calories macronutrient breakdown (x% fat, y% carbs, z% protein)
* [ ] Daily summary export (pdf/excel)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details

## Acknowledgments
* [DotKernel](https://www.dotkernel.com/)
* [Bootstrap](https://getbootstrap.com/)
* [FontAwesome](https://fontawesome.com/)
