<div class="box">
    <div class="box-header">
        <h3 class="box-title"><i class="fa fa-flask"></i> <?=$this->lang->line('panel_title')?></h3>
        <ol class="breadcrumb">
            <li><a href="<?=base_url("dashboard/index")?>"><i class="fa fa-laptop"></i> <?=$this->lang->line('menu_dashboard')?></a></li>
            <li class="active"><?=$this->lang->line('menu_mark')?></li>
        </ol>
    </div>
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12"></div>
        </div>
    </div>
</div>
<div class="box">
    <div class="box-body">
        <div class="row">
            <div class="col-sm-12">
                <canvas id="myChartLine"></canvas>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript" src="<?php echo base_url('assets/chartjs/chart.js'); ?>"></script>
<script type="text/javascript">
    var ctx = document.getElementById("myChart");
    var ctxline = document.getElementById("myChartLine");

    var myLineChart = new Chart(ctxline, {
        type: 'line',
        data: {
            labels: [ <?php

                        foreach ($exams as $exam) {
                            echo "\"";
                            echo $exam->exam;
                            echo "\",";
                        }

                     ?>
                ],
            datasets: [
                {
                    label: "Голч дүн",
                    fill: false,
                    lineTension: 0.1,
                    backgroundColor: "rgba(75,192,192,0.4)",
                    borderColor: "rgba(75,192,192,1)",
                    borderCapStyle: 'butt',
                    borderDash: [],
                    borderDashOffset: 0.0,
                    borderJoinStyle: 'miter',
                    pointBorderColor: "rgba(75,192,192,1)",
                    pointBackgroundColor: "#fff",
                    pointBorderWidth: 1,
                    pointHoverRadius: 5,
                    pointHoverBackgroundColor: "rgba(75,192,192,1)",
                    pointHoverBorderColor: "rgba(220,220,220,1)",
                    pointHoverBorderWidth: 2,
                    pointRadius: 1,
                    pointHitRadius: 10,
                    data: [
                        <?php
                            $map1 = function($r) { return intval($r->examID);};
                            $marks_examsID = array_map($map1, $marks);
                            $max_semester = max($marks_examsID);
                            $map2 = function($r) { return intval($r->examID);};
                            $examsID = array_map($map2, $exams);
                            $map3 = function($r) { return array("mark" => intval($r->mark), "semester"=>$r->examID);};
                            $all_marks = array_map($map3, $marks);
                            $map4 = function($r) { return array("gradefrom" => $r->gradefrom, "gradeupto" => $r->gradeupto);};
                            $grades_check = array_map($map4, $grades);
                            foreach ($exams as $exam) {
                                 if($exam->examID <= $max_semester) {
                                    $check = array_search($exam->examID, $marks_examsID);
                                    if($check>=0) {
                                        $f = 0;
                                        foreach ($grades_check as $key => $range) {
                                            foreach ($all_marks as $value) {
                                                if($value['semester'] == $exam->examID ) {
                                                    if($value['mark']>=$range['gradefrom'] && $value['mark']<=$range['gradeupto'])
                                                    {
                                                        $f=1;
                                                    }
                                                }
                                            }
                                            if($f==1)
                                            {
                                                break;
                                            }
                                        }
                                        if(count($grades) && $f == 1) {
                                            $gpa = 0;
                                            $count = 0;
                                            foreach ($marks as $mark) {
                                                if($exam->examID == $mark->examID) {
                                                    if ($mark->mark != null) {

                                                        if(count($grades)) {

                                                            foreach ($grades as $grade) {
                                                                if($grade->gradefrom <= $mark->mark && $grade->gradeupto >= $mark->mark) {

                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        $gpa = $gpa + $grade->point;

                                                        $count++;

                                                    }
                                                }
                                            }
                                        }
                                    }
                                }

                                echo $gpa / $count;
                                echo ",";
                            }

                        ?>
                    ],
                    spanGaps: false,
                }
            ]
        },
        options: {
            responsive: true,
            legend: {
                position: 'top',
            },
            hover: {
                mode: 'label'
            },
            scales: {
                xAxes: [{
                        display: true,
                        scaleLabel: {
                            display: true,
                            labelString: 'Хичээлийн жил'
                        }
                    }],
                yAxes: [{
                        display: true,
                        ticks: {
                            beginAtZero: true,
                            steps: 10,
                            stepValue: 5,
                            max: 4.0
                        },
                        scaleLabel: {
                            display: true,
                            labelString: 'Үнэлгээний голч дүн'
                        }
                    }]
            },
            title: {
                display: true,
                text: 'Оюутны дүнгийн үзүүлэлт'
            }
        }
    });
  
    $('#classesID').change(function() {
        var classesID = $(this).val();
        if(classesID == 0) {
            $('#hide-table').hide();
        } else {
            $.ajax({
                type: 'POST',
                url: "<?=base_url('mark/mark_list')?>",
                data: "id=" + classesID,
                dataType: "html",
                success: function(data) {
                    window.location.href = data;
                }
            });
        }
    });
</script>
