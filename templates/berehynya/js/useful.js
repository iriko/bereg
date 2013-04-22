jQuery(document).ready(function() {
    //Для все картинок на сайте
    jQuery("img").removeAttr("title");
    //Добавляем клас sub-menu
    jQuery('.header-menu .deeper > ul').addClass('sub-menu');
})