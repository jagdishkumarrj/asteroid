<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title>Astroid | Neo</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" integrity="sha512-mSYUmp1HYZDFaVKK//63EcZq4iFWFjxSL+Z3T/aCt4IO9Cejm03q3NKKYN6pFQzY0SBOr8h+eCIAZHPXcpZaNw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
        <style>
            #loader{
                position: fixed;
                display: block;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                text-align: center;
                opacity: 0.7;
                background-color: rgb(0 0 0 / 75%);;
                z-index: 99;
            }
            .spinner-grow{
                position: absolute;
                top: 40%;
                left: 50%;
            }
        </style>
    </head>
    <body>
        <div id="loader">
            <div class="spinner-grow text-primary" style="width: 3rem; height: 3rem;" role="status">
                <!-- <span class="sr-only">Loading...</span> -->
            </div>
        </div>
        <div class="container mt-5">
            <form id="dateForm" method="post" autocomplete="off">
                <div class="row mb-3">
                    <label for="start_date" class="col-sm-2 col-form-label">Start Date</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" class="form-control" id="start_date" name="start_date" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="end_date" class="col-sm-2 col-form-label">End Date</label>
                    <div class="col-sm-10 col-md-6">
                        <input type="text" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>
        </div>
        <div class="container mt-5">
            <div class="row">
                <div class="col-12">
                    <div id="message">

                    </div>
                    <canvas id="myChart" height="100"></canvas>
                </div>
            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js" integrity="sha512-T/tUfKSV1bihCnd+MxKD0Hm1uBBroVYBOYSk1knyvQ9VyZJpc/ALb4P0r6ubwVPSGB2GvjeoMAJJImBG12TiaQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script src="https://cdn.jsdelivr.net/npm/chart.js@3.4.1/dist/chart.min.js"></script>
        <script>
            $(document).ready(function()
            {
                $("#loader").hide();
                $('#start_date').datepicker({
                    format: 'yyyy-mm-dd',
                    autoclose: true,
                });
                $('#end_date').datepicker({
                    format: 'yyyy-mm-dd',
                });
            });
        </script>
        <script>
            var cartText = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(cartText, { type: 'bar', data: [], options: [] });
            $('#dateForm').on("submit",function(e)
            {
                e.preventDefault();
                
                $("#loader").show();
                var start_date = $("#start_date").val();
                var end_date = $("#end_date").val();
                $.ajax({
                    url:"/getAstroidDetail",
                    data:{"start_date":start_date,"end_date":end_date},
                    type:"get",
                    dataType:"json",
                    success:function(response)
                    {
                        $("#loader").hide();
                        $("#message").html('');
                        console.log(response);
                        myChart.destroy();
                        if(response.status==true)
                        {
                            var dates = response.data.dates;
                            var astroids = response.data.astroid_by_dates;
                            
                            $('#message').html('<span><b>Fastest Asteroid Id & Speed(in KM/Hour) </b></span>'+
                                    '<span class="d-block">'+response.data.fastestAseroidId+' "=" '+response.data.fastestAseroid+' </span>'+
                                    '<span class="d-block"><b>Closest Asteroid Id & Distance(in KM)</b></span>'+
                                    '<span class="d-block">'+response.data.closestAseroidId+' "=" '+response.data.closestAseroid+'</span>'+
                                    '<span class="d-block"><b>Average Size of Asteroids(in KM)</b></span>'+
                                    '<span class="d-block">'+response.data.average_size+'</span>');
                            
                            myChart = new Chart(cartText, {
                                type: 'bar',
                                data: {
                                    labels: dates,
                                    datasets: [{
                                        label: '# of Asteroids',
                                        data:astroids,
                                        backgroundColor: [
                                            'Blue',
                                        ],
                                        borderColor: [
                                            'Blue',
                                        ],
                                        borderWidth: 1
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    scales: {
                                        y: {
                                            beginAtZero: true
                                        }
                                    }
                                }
                            });
                        }else{
                            $("#message").html('<div class="alert alert-danger alert-dismissible fade show" role="alert">'+
                                response.data+
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">'+
                                '<span aria-hidden="true">&times;</span>'+
                            '</button></div>');
                        }
                    }
                });
            });
            </script>
    </body>
</html>