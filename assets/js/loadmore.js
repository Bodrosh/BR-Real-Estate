const delay = ms => {
    return new Promise(resolve => {
        setTimeout(() => {
            resolve()
        }, ms)
    })
}

jQuery(function($){

    $('.show-more__button').click(function(){
        $(this).children('div').text('Загружаю...'); // изменяем текст кнопки, вы также можете добавить прелоадер
        var data = {
            'action': 'loadmore',
            'query': true_posts,
            'page' : current_page
        };
        $.ajax({
            url:ajaxurl, // обработчик
            data:data, // данные
            type:'POST', // тип запроса
            success:function(data){
                if( data ) {
                    $('.show-more__button > div').text('Загрузить ещё');
                    $('.page-loop.with-filter').append(data); // вставляем новые посты

                    // скролл к загруженным объектам
                    let $dataHtml = $.parseHTML(data)
                    if($dataHtml.length > 0) {
                        let $firstElementId = $dataHtml[0].id
                        delay(100)
                            .then(() => {
                                let destination = $('#' + $firstElementId).offset().top;
                                $('html, body').animate({ scrollTop: destination }, 600);
                            })
                    }
                    current_page++; // увеличиваем номер страницы на единицу
                    if (current_page == max_pages) $(".show-more__button").remove(); // если последняя страница, удаляем кнопку
                } else {
                    $('.show-more__button').remove(); // если мы дошли до последней страницы постов, скроем кнопку
                }
            }
        });
    });
});