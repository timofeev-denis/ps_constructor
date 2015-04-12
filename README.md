# ps_constructor
Prestashop goods constructor

TODO:
Add component: save data to 'goods_buffer'
Edit component: show component category & subcategory (connect to PS product)

IMPORTANT
To show prices in currency that is not the shop's default currency you should:
- Add currency you want to see in frontoffice
- Find file classes/controller/FrontController.php and open it with text editor
- Find string $currency = Tools::setCurrency($this->context->cookie);
- Insert this before that string: $this->context->cookie->id_currency = 1; //"1" is the id of frontend currency
