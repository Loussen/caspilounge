    **Api documentation**
       
Qeyd: Bütün api-lərə GET parametri olaraq, main_lang=4 göndərilsin. (gələcəkdə sayt çox dilli olmağı üçün)

1. Slider top - http://api.korpus.az/api/sliders_top.php?main_lang=4 +

2. Private events - http://api.korpus.az/api/pevents.php?main_lang=4 +

3. Slider middle - http://api.korpus.az/api/sliders_middle.php?main_lang=4 +

4. Slider bottom - http://api.korpus.az/api/sliders_bottom.php?main_lang=4 +

5. Menus slider - http://api.korpus.az/api/menus_slider.php?main_lang=4 +

6. Categories - http://api.korpus.az/api/categories.php?main_lang=4 +

7. Menus - http://api.korpus.az/api/menus.php?main_lang=4 || http://api.korpus.az/api/menus.php?main_lang=4&category_id=1 +

8. Alboms - http://api.korpus.az/api/alboms.php?main_lang=4 +

9. Gallery - http://api.korpus.az/api/gallery.php?main_lang=4 || http://api.korpus.az/api/gallery.php?main_lang=4&albom_id=1 +

10. Contacts - http://api.korpus.az/api/contacts.php?main_lang=4 +

11. Catering form - http://api.korpus.az/api/catering.php - { "name" : "Fuad", "surname" : "Hasanli", "month" : "05", "day" : "02", "year" : "2019", "email" : "fhesenli92@gmail.com", "subject" : "Salam", "message" : "Salam salam" } +

12. http://api.korpus.az/api/init.php - HTTP_SECRET - key: secret, value: token +

13. http://api.korpus.az/api/add_cart.php - { "food_id" : 12, "special_req" : "With sous", "quantity" : 2 } - Send header secret key +

14. http://api.korpus.az/api/update_cart.php - { "food_id" : 12, "special_req" : "With sous updated", "quantity" : 3 } - Send header secret key +

15. http://api.korpus.az/api/delete_cart.php - { "food_id" : 12 } - Send header secret key +

16. http://api.korpus.az/api/delete_all_cart.php - { "token" : token }

17. http://api.korpus.az/api/confirm_order.php - { "city" : "Baku", "no" : "15", "floor" : "5", "street" : "Resid Behbudov", "apt" : "Residence", "firstname" : "Fuad", "lastname" : "Hasanli", "phone" : "+994506877836", "email" : "fhesenli92@gmail.com", "pay_type" : 1, "special_req" : "Please quite" } - Send header secret

18. http://api.korpus.az/api/get_cart.php?main_lang=4 - { "token" : token } +