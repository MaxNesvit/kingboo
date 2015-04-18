var LANGS = {};

LANGS.ru = {
    image_list_title : "Фотографии",
    image_list_empty : "Нет загруженных фотографий",
    
    image_add : "Добавить фотографию",
    delete_confirm : 'Вы уверены, что хотите удалить фотографию?\n' +
    'В случае продолжения фотография будет удалена безвозвратно!\n',
    
};

LANGS.en = {
    image_list_title : "Photos",
    image_list_empty : "No photos uploaded",
    
    image_add : "Add photo",
    delete_confirm : 'Are you sure want to delete this photo?\n',
    
};

function t(v, l) {
    if (typeof l == 'undefined' && typeof window.LANG !== 'undefined') {
        l = window.LANG;
    }

    if (typeof LANGS[l][v] != 'undefined') {
        return LANGS[l][v]
    } else {
        return v;
    }
}