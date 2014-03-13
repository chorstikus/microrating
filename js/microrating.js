jQuery(document).ready(function($) {
    
    var currentCount = parseInt($('.count').text(), 10),
        currentAverage = parseInt($('.average').text(), 10);

    if(!ajax_object.voted_value) {
        $('.star-container').raty({
            path: ajax_object.plugins_url,
            click: function(value, link) {
                var newSum = parseInt(ajax_object.sum, 10) + parseInt(value, 10),
                    newCount = currentCount+1,
                    newAverage = Math.round( newSum / newCount ),
                    data = {
                        action: 'add_rating',
                        vote: value,
                        post_id: ajax_object.post_id
                    };

                $('.count').text( newCount );
                $('.average').text( newAverage );

                $.post(
                    ajax_object.ajax_url,
                    data,
                    function(response) {
                        $('.star-container').raty({
                            path: ajax_object.plugins_url,
                            readOnly: true,
                            score: value,
                        });
                    }
                );
            }
        });
    } else {
        $('.star-container').raty({
            path: ajax_object.plugins_url,
            readOnly: true,
            score: ajax_object.voted_value,
        });
    }


});