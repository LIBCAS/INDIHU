
const postsElement = $('.posts');

let page = 1;

if(postsElement != undefined){
    $(document).ready(function(){

        $(postsElement).on('click', '.pagination__item', function(){
            page = $(this).data('page');
            findPosts();
        })

    });
}

function findPosts(){
    $.ajax({
        url: "/html/posts",
        type: "get",
        data: {
            'categories': categories,
            'count': count,
            'page': page
        }
    }).done(function (response){  
        $('.posts__items').html(response['posts']);
        $('.posts__pagination').html(response['paginator']);
    });
}