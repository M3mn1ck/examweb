$(document).ready(function () {
    $('#orderForm').submit(function (e) {
        e.preventDefault();
        var routeName = $('#routeName').val();
        var guideName = $('#guideName').val();
        var excursionDate = $('#excursionDate').val();
        var startTime = $('#startTime').val();
        var duration = $('#duration').val();
        var groupSize = $('#groupSize').val();


        $.ajax({
            type: 'POST',
            url: 'send.php',
            data: {
                routeName: routeName,
                guideName: guideName,
                excursionDate: excursionDate,
                startTime: startTime,
                duration: duration,
                groupSize: groupSize
            },
            success: function (response) {

                console.log(response);
            },
            error: function (error) {

                console.error(error);
            }
        });
    });
});
