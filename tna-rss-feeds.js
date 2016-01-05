/**
 * Created by chrisbishop on 05/01/2016.
 */

$.get( 'http://blog.nationalarchives.gov.uk/feed/', function (data) {
    $(data).find("item").each(function () {
        var el = $(this);

        console.log("------------------------");
        console.log("title      : " + el.find("title").text());
        console.log("author     : " + el.find("author").text());
        console.log("description: " + el.find("description").text());
    });
});