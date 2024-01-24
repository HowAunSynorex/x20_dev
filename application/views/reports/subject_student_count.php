<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold"><?php echo $thispage['title']; ?></h4>
                </div>
            </div>





            <div class="row pb-4">
              <div class="col-2"></div>
              <div class="col-1 d-flex align-items-center justify-content-end">
                <h6>Level:</h6>
              </div>
              <div class="col-2">
                <select class="form-control text-center" id="filter_level" onchange="filter()" style="cursor:pointer;">
                  <option selected disabled> -- </opiton>
                  <?php foreach($all_form as $item){ ?>
                    <option value="<?= $item->pid ?>" <?= $course==$item->pid?'selected':'' ?>><?= $item->title ?></option>
                  <?php } ?>
                </select>
              </div>
              <div class="col-1 d-flex align-items-center justify-content-end">
                <h6>Month:</h6>
              </div>
              <div class="col-2">
                <select class="form-control text-center" id="filter_month" onchange="filter()" style="cursor:pointer;">
                <?php
                  $monthNames = array('January','February','March','April','May','June','July','August','September','October','November','December');
                  foreach ($monthNames as $index => $month) {
                      $monthNumber=sprintf("%02d", $index + 1);
                ?>
                  <option value="<?= $monthNumber ?>" <?= $monthNumber==$month?'selected':'' ?>><?= $month; ?></option>
                <?php
                  }
                ?>
                </select>
              </div>
              <div class="col-1 d-flex align-items-center justify-content-end">
                <h6>Year:</h6>
              </div>
              <div class="col-2">
                <select class="form-control text-center" id="filter_year" onchange="filter()" style="cursor:pointer;">
                <?php
                  $year_now=date('Y');
                  $year_start=$year_now - 10;
                  for($year_=$year_now; $year_>=$year_start;$year_--) {
                ?>
                  <option value="<?= $year_ ?>" <?= $year==$year_?'selected':'' ?>><?= $year_ ?></option>
                <?php
                  }
                ?>
                </select>
              </div>
              <div class="col-1"></div>
            </div>



            <div class="row">
              <div class="col-12">
                <div class="table-responsive">
                  <table class="table">
                    <thead>
                      <tr>
                        <th class="col-2 text-center">#</th>
                        <th class="col-3">Subject(s)</th>
                        <th class="col-1 text-center">Student(s)</th>
                        <th class="col-3 text-center"></th>
                        <th class="col-3 text-center">TOTAL</th>
                      <tr>
                    </thead>
                    <tbody>
                      <?php $total = 0; ?>
                      <?php $count=1; ?>
                      <?php foreach($all_course as $course){ ?>
                        <tr>
                          <td class="text-center"><?= $count ?></td>
                          <td class="">
                            <h6><?= $course['title'] ?></h6>
                          </td>
                          <td class="text-center">
                            <button class="btn btn-info btn-sm" type="button" title="View" data-toggle="collapse" data-target="#student_list<?= $course['pid'] ?>" <?= count($course['student_name_list'])>0?'':'disabled' ?>>
                              <i class="fa fa-eye" aria-hidden="true"></i>
                            </button>
                          </td>
                          <td class="text-center">
                            <div class="collapse" id="student_list<?= $course['pid'] ?>">
                              <div class="card bg-transparent border-0">
                                  <div class="card-body">
                                      <div class="">
                                        <?php foreach($course['student_name_list'] as $student){ ?>
                                          <div class="row border-bottom">
                                              <div class="col-8">
                                                <small><?= $student['student_name_en'] ?></small>
                                              </div>
                                              <div class="col-4">
                                                <small><?= $student['student_name_cn'] ?></small>
                                              </div>
                                          </div>
                                        <?php } ?>
                                      </div>
                                  </div>
                              </div>
                            </div>
                          </td>
                          <td class="text-center">
                            <h6><?= $course['student_count'] ?></h6>
                          </td>
                        </tr>
                        <?php $total += $course['student_count'] ?>
                        <?php $count++ ?>
                      <?php } ?>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <hr>
            <div class="row">
              <div class="col-9"></div>
              <div class="col-3 text-center">Total: <?= $total ?></div>
            </div>

        </div>


        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<script>
  const filter = () => {
    var filter_level = $("#filter_level").val();
    var filter_month = $("#filter_month").val();
    var filter_year = $("#filter_year").val();
    window.location.href="<?php echo site_url(); ?>reports/subject_student_count/"+filter_level+"/"+filter_month+"/"+filter_year;
  }
</script>