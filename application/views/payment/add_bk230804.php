<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

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
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <form id="main-form" method="post" onsubmit="Loading(1)">

                <div class="row">
                    <div class="col-md-6">

                        <?php if (isset($_GET['by_class'])) { ?>
                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Class</label>
                                <div class="col-md-9">
                                    <select class="form-control select2"
                                            onchange="window.location.href='<?php echo base_url('payment/add'); ?>'+'?by_class&class='+this.value"
                                            name="class" required>
                                        <option value="">-</option>
                                        <?php foreach ($classes as $e) { ?>
                                            <option value="<?php echo $e['pid']; ?>" <?php if (isset($_GET['class']))
                                            {
                                                if ($_GET['class'] == $e['pid'])
                                                {
                                                    echo 'selected';
                                                }
                                            } ?>><?php echo $e['title']; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (!isset($_GET['class']) || empty($_GET['class'])) { ?>
                                        <div class="alert alert-warning mt-2">Please select a class</div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>

                        <?php
                        if (isset($_GET['by_class']) && isset($_GET['class']) && !empty($_GET['class']))
                        {
                            ?>
                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Student</label>
                                <div class="col-md-9">
                                    <select class="form-control select2"
                                            onchange="window.location.href='<?php echo base_url('payment/add/'); ?>'+this.value+'?by_class&class=<?php echo $_GET['class']; ?>'"
                                            name="student" required>
                                        <option value="">-</option>
                                        <?php foreach ($student as $e) { ?>
                                            <option value="<?php echo $e['user']; ?>" <?php if (isset($id))
                                            {
                                                if ($id == $e['user'])
                                                {
                                                    echo 'selected';
                                                }
                                            } ?>><?php echo datalist_Table('tbl_users', 'fullname_en', $e['user']) . '(' . datalist_Table('tbl_users', 'code', $e['user']) . ')'; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (empty($id)) { ?>
                                        <div class="alert alert-warning mt-2">Please select a student</div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                        }
                        elseif (!isset($_GET['by_class']))
                        {
                            ?>
                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Student</label>
                                <div class="col-md-9">
                                    <select class="form-control select2"
                                            onchange="window.location.href='<?php echo base_url('payment/add/'); ?>'+this.value"
                                            name="student" required>
                                        <option value="">-</option>
                                        <?php foreach ($student as $e) { ?>
                                            <option value="<?php echo $e['pid']; ?>" <?php if (isset($id))
                                            {
                                                if ($id == $e['pid'])
                                                {
                                                    echo 'selected';
                                                }
                                            } ?>><?php echo $e['fullname_en'] . ' (' . $e['code'] . ')' . ' (' . $e['pid'] . ')'; ?></option>
                                        <?php } ?>
                                    </select>
                                    <?php if (empty($id)) { ?>
                                        <div class="alert alert-warning mt-2">Please select a student</div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php
                        }
                        ?>

                    </div>
                    <div class="col-md-6">

                        <?php /*if(isset($id) && !empty($id)) {?>

                        <div class="form-group row">
                            <label class="col-form-label col-md-3">Status</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="status" required>
                                    <?php foreach (datalist('payment_status') as $k => $v) { ?>
                                    <option value="<?php echo $k; ?>"><?php echo $v['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>

                        <?php }*/ ?>

                    </div>
                </div>

                <?php
                if (isset($id) && !empty($id))
                {

                    $student_data = $this->tbl_users_model->view($id)[0];
                    $std_unpaid_result = std_unpaid_result2($id);
                    $std_default_result = std_default_result($id);


                    $new_unpaid_class = [];
                    $material_fee = [];
                    $subsidy_fee = [];
                    $with_class_bundle = [];
                    $without_class_bundle = [];

                    $default_material_fee = [];
                    $default_subsidy_fee = [];
                    $with_default_class_bundle = [];
                    $without_default_class_bundle = [];

                    $total_material_fee = 0;
                    $total_subsidy_fee = 0;

                    $total_default_subsidy_fee = 0;

                    //get default class data (not check payment make or not)
                    if (isset($std_default_result['result']['default_class']))
                    {
                        foreach ($std_default_result['result']['default_class'] as $e)
                        {
                            $default_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
                                'course' => datalist_Table('tbl_classes', 'course', $e['class'])
                            ]);

                           // print_array($default_class_bunlde);
                            if (count($default_class_bunlde) > 0)
                            {
                                $default_class_bunlde = $default_class_bunlde[0];
                                $with_default_class_bundle[$default_class_bunlde['parent']]['data'][] = $e;
                            }
                            else
                            {
                                $without_default_class_bundle[]['data'] = $e;
                            }
                        }


                        foreach ($with_default_class_bundle as $k => $v)
                        {
                            $check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
                                'parent' => $k,
                                'qty' => count($v['data'])
                            ]);

                            /*
                            echo branch_now('pid');
                            echo '---'.$k;
                            echo '---'. count($v['data']);
                            print_array($check_bundle_price);
                            die;
                            */
                            if (count($check_bundle_price) > 0)
                            {
                                $check_bundle_price = $check_bundle_price[0];
                                $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
                                $default_material_fee[] = [
                                    'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                    'fee' => $check_bundle_price['material']
                                ];

                                foreach ($v['data'] as $k2 => $v2)
                                {
                                    $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
                                    $with_default_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                    $with_default_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
                                    $with_default_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
                                }
                            }
                            else
                            {
                                $check_bundle_price = $this->db->query('
                                    SELECT * FROM log_join
                                    WHERE is_delete = 0
                                    AND type = "class_bundle_price"
                                    AND branch = "' . branch_now('pid') . '"
                                    AND parent = "' . $k . '"
                                    AND qty < ' . count($v['data']) . '
                                    ORDER BY qty DESC
                                ')->result_array();
                                if (count($check_bundle_price) > 0)
                                {
                                    $check_bundle_price = $check_bundle_price[0];
                                    $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
                                    for ($i = 0; $i < floor(count($v['data']) / $check_bundle_price['qty']); $i++)
                                    {
                                        $default_material_fee[] = [
                                            'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                            'fee' => $check_bundle_price['material']
                                        ];
                                    }
                                    foreach ($v['data'] as $k2 => $v2)
                                    {
                                        if ($k2 >= $check_bundle_price['qty'] * floor(count($v['data']) / $check_bundle_price['qty']))
                                        {
                                            $without_default_class_bundle[0]['data'][] = $v2;
                                            unset($with_default_class_bundle[$k]['data'][$k2]);
                                        }
                                        else
                                        {
                                            $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
                                            $with_default_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                            $with_default_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
                                            $with_default_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
                                        }
                                    }
                                }
                            }
                        }


                        foreach ($with_default_class_bundle as $k => $v)
                        {
                            //get actual class qty , array is combine with multi outstanding month now
                            $actual_class_lists = array();
                            foreach ($v['data'] as $v_row)
                            {
                                $actual_class_lists[$v_row['id']] = $v_row;
                            }
                            $actual_class_count = count($actual_class_lists);

                            $bundle_subsidy = $this->db->query('
									SELECT * FROM log_join
									WHERE is_delete = 0
									AND type = "class_bundle_price"
									AND branch = "' . branch_now('pid') . '"
									AND parent = "' . $k . '"
									AND subsidy > 0
									AND qty <= ' . $actual_class_count . '
									ORDER BY qty DESC
								')->result_array();


                            if (count($bundle_subsidy) > 0)
                            {
                                $bundle_subsidy = $bundle_subsidy[0];
                                for ($i = 0; $i < floor(count($v['data']) / $check_bundle_price['qty']); $i++)
                                {

                                    $default_subsidy_fee[] = [
                                        'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                        'fee' => $bundle_subsidy['subsidy']
                                    ];

                                }

                                foreach ($v['data'] as $k2 => $v2)
                                {
                                    $with_default_class_bundle[$k]['data'][$k2]['subsidy_fee'] = $bundle_subsidy['subsidy'];
                                }
                            }
                        }

                        foreach ($default_subsidy_fee as $e)
                        {
                            $total_default_subsidy_fee += $e['fee'];
                        }
                    }

                    $new_default_unpaid_class = array_merge($with_default_class_bundle, $without_default_class_bundle);


                    if ($std_unpaid_result['count'] > 0)
                    {

                        if (isset($std_unpaid_result['result']['class']))
                        {

                            foreach ($std_unpaid_result['result']['class'] as $e)
                            {
                                $check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
                                    'course' => datalist_Table('tbl_classes', 'course', $e['class'])
                                ]);
                                if (count($check_class_bunlde) > 0)
                                {
                                    $check_class_bunlde = $check_class_bunlde[0];
                                    $with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e;
                                }
                                else
                                {
                                    $without_class_bundle[]['data'] = $e;
                                }
                            }


                            foreach ($with_class_bundle as $k => $v)
                            {

                                //get actual class qty , array is combine with multi outstanding month now
                                $actual_class_lists = array();
                                foreach ($v['data'] as $v_row)
                                {
                                    $actual_class_lists[$v_row['id']] = $v_row;
                                }
                                $actual_class_count = count($actual_class_lists);
                                $actual_material_count = count($v['data']) / $actual_class_count;
                                $actual_subsidy_count = count($v['data']) / $actual_class_count;

                                $check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
                                    'parent' => $k,
                                    'qty' => $actual_class_count
                                ]);

                                if (count($check_bundle_price) > 0)
                                {
                                    $check_bundle_price = $check_bundle_price[0];
                                    $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];

                                    for ($a = 0; $a < $actual_material_count; $a++)
                                    {
                                        $material_fee[] = [
                                            'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                            'fee' => $check_bundle_price['material']
                                        ];
                                    }

                                    foreach ($v['data'] as $k2 => $v2)
                                    {
                                        $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
                                        $with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                        $with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
                                        $with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
                                        $with_class_bundle[$k]['data'][$k2]['material_fee'] = $check_bundle_price['material'];
                                    }


                                }
                                else
                                {
                                    $check_bundle_price = $this->db->query('
										SELECT * FROM log_join
										WHERE is_delete = 0
										AND type = "class_bundle_price"
										AND branch = "' . branch_now('pid') . '"
										AND parent = "' . $k . '"
										AND qty < ' . $actual_class_count . '
										ORDER BY qty DESC
									')->result_array();
                                    if (count($check_bundle_price) > 0)
                                    {
                                        $check_bundle_price = $check_bundle_price[0];
                                        $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
                                        for ($i = 0; $i < floor($actual_class_count / $check_bundle_price['qty']); $i++)
                                        {

                                            for ($a = 0; $a < $actual_material_count; $a++)
                                            {
                                                $material_fee[] = [
                                                    'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                                    'fee' => $check_bundle_price['material']
                                                ];
                                            }
                                        }

                                        foreach ($v['data'] as $k2 => $v2)
                                        {
                                            if ($k2 >= $check_bundle_price['qty'] * floor($actual_class_count / $check_bundle_price['qty']))
                                            {
                                                $without_class_bundle[0]['data'][] = $v2;
                                                unset($with_class_bundle[$k]['data'][$k2]);
                                            }
                                            else
                                            {
                                                $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
                                                $with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                                $with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
                                                $with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
                                                $with_class_bundle[$k]['data'][$k2]['material_fee'] = $check_bundle_price['material'];

                                            }
                                        }
                                    }
                                }
                            }


                            foreach ($with_class_bundle as $k => $v)
                            {
                                //get actual class qty , array is combine with multi outstanding month now
                                $actual_class_lists = array();
                                foreach ($v['data'] as $v_row)
                                {
                                    $actual_class_lists[$v_row['id']] = $v_row;
                                }
                                $actual_class_count = count($actual_class_lists);

                                $bundle_subsidy = $this->db->query('
									SELECT * FROM log_join
									WHERE is_delete = 0
									AND type = "class_bundle_price"
									AND branch = "' . branch_now('pid') . '"
									AND parent = "' . $k . '"
									AND subsidy > 0
									AND qty <= ' . $actual_class_count . '
									ORDER BY qty DESC
								')->result_array();


                                if (count($bundle_subsidy) > 0)
                                {
                                    $bundle_subsidy = $bundle_subsidy[0];
                                    for ($i = 0; $i < floor($actual_class_count / $bundle_subsidy['qty']); $i++)
                                    {
                                        for ($a = 0; $a < $actual_subsidy_count; $a++)
                                        {
                                            $subsidy_fee[] = [
                                                'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                                'fee' => $bundle_subsidy['subsidy']
                                            ];
                                        }
                                    }

                                    foreach ($v['data'] as $k2 => $v2)
                                    {
                                        $with_class_bundle[$k]['data'][$k2]['subsidy_fee'] = $bundle_subsidy['subsidy'];
                                    }
                                }
                            }

                            foreach ($default_material_fee as $e)
                            {
                                $total_material_fee += $e['fee'];
                            }

                            foreach ($subsidy_fee as $e)
                            {
                                $total_subsidy_fee += $e['fee'];
                            }
                        }
                    }


                    $new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);
                    ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-form-label col-md-3">Form</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext"><?php echo datalist_Table('tbl_secondary', 'title', $student_data['form']); ?></p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">Transportation</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext"><?php echo empty($student_data['transport']) ? '-' : datalist_Table('tbl_secondary', 'title', $student_data['transport']); ?></p>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">Childcare</label>
                                <div class="col-md-9">
                                    <p class="form-control-plaintext"><?php echo empty($student_data['childcare']) ? '-' : datalist_Table('tbl_secondary', 'title', $student_data['childcare']); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="mb-2 font-weight-bold">Timetable</label>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Day</th>
                                    <th>Time Range</th>
                                    <th>Subject</th>
                                    <th>Form</th>
                                    <th>Teacher</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $sql = '
										SELECT
											l2.time_range, 
											l2.qty AS day,
											c.title AS subject,
											s.title AS form,
											u.fullname_en AS teacher
										FROM log_join l
										INNER JOIN log_join l2
										ON l.sub_class = l2.id
										AND l.is_delete = 0
										AND l2.is_delete = 0
										AND l.type = "join_class"
										AND l2.type = "class_timetable"
										AND l.branch = "' . branch_now('pid') . '"
										AND l2.branch = "' . branch_now('pid') . '"
										AND l.active = 1
										AND l.user = "' . $id . '"
										INNER JOIN tbl_classes c
										ON l2.class = c.pid
										AND c.is_delete = 0
										AND c.branch = "' . branch_now('pid') . '"
										INNER JOIN tbl_secondary s
										ON c.course = s.pid
										AND s.is_delete = 0
										AND s.type = "course"
										INNER JOIN tbl_users u
										ON c.teacher = u.pid
										AND u.is_delete = 0
										AND u.type = "teacher"
									';

                                $query = $this->db->query($sql)->result_array();
                                $i = 0;
                                foreach ($query as $e)
                                {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo datalist('day_name')[$e['day']]['name']; ?></td>
                                        <td><?php echo $e['time_range']; ?></td>
                                        <td><?php echo $e['subject']; ?></td>
                                        <td><?php echo $e['form']; ?></td>
                                        <td><?php echo $e['teacher']; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="mb-2 font-weight-bold">Current Monthly Fee (Unbilled)</label>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Amount (RM)</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 0;
                                $total_default_fee = 0;
                                $total_default_discount = 0;

                                foreach ($new_default_unpaid_class as $k => $e)
                                {
                                    foreach ($e['data'] as $e2)
                                    {	
                                        $i++;
                                        
                                        if(!isset($e2['amount'])) $e2['amount'] = 0;
                                        if(!isset($e2['discount'])) $e2['discount'] = 0;
                                        
                                        $total_default_fee += ((int)$e2['amount']);
                                        $total_default_discount += ((int)$e2['discount']);
										
										if($e2['amount'] != 0) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $e2['title']; ?></td>
                                            <td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',');; ?></td>
                                        </tr>
                                        <?php
										}
                                    }
                                }

                                /*
                                 foreach ($std_default_result['result'] as $k => $e)
                                 {
                                     if ($k === "default_class")
                                     {
                                         foreach ($e as $e2)
                                         {
                                             $i++;
                                             $total_default_fee += $e2['amount'];
                                             ?>
                                             <tr>
                                                 <td><?php echo $i; ?></td>
                                                 <td><?php echo $e2['title']; ?></td>
                                                 <td class="text-right"><?php echo number_format($e2['amount'], 2, '.', ',');; ?></td>
                                             </tr>
                                             <?php
                                         }
                                     }
                                 }
                                */

                                ?>
                                <? if ($student_data['childcare_title'] != ""): ?>
                                <?
                                    $i++;
                                    $total_default_fee += $student_data['childcare_price'];

                                ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $student_data['childcare_title']; ?></td>
                                        <td class="text-right"><?php echo number_format($student_data['childcare_price'], 2, '.', ',');; ?></td>
                                    </tr>

                                <? endif; ?>

                                <? if ($student_data['transport_title'] != ""): ?>
                                    <?
                                    $i++;
                                    $total_default_fee += $student_data['transport_price'];

                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $student_data['transport_title']; ?></td>
                                        <td class="text-right"><?php echo number_format($student_data['transport_price'], 2, '.', ',');; ?></td>
                                    </tr>

                                <? endif; ?>

                                <?php

                                foreach ($default_material_fee as $k => $v)
                                {
                                    if ($v['fee'] > 0)
                                    {
                                        $i++;
                                        $total_default_fee += $v['fee'];
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $v['title']; ?></td>
                                            <td class="text-right"><?php echo number_format($v['fee'], 2, '.', ',');; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>

                                <?php

                                if ($total_default_discount > 0)
                                {
                                    $i++;
                                    $total_default_fee -= $total_default_discount;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td>Discount</td>
                                        <td class="text-right">
                                            -<?php echo number_format($total_default_discount, 2, '.', ',');; ?></td>
                                    </tr>
                                    <?
                                }

                                foreach ($default_subsidy_fee as $k => $v)
                                {
                                    if ($v['fee'] > 0)
                                    {
                                        $i++;
                                        $total_default_fee -= $v['fee'];
                                        ?>
                                        <tr>
                                            <td><?php echo $i; ?></td>
                                            <td><?php echo $v['title']; ?></td>
                                            <td class="text-right">
                                                -<?php echo number_format($v['fee'], 2, '.', ',');; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }
                                ?>
								
                                <tr>
                                    <td></td>
                                    <td class="text-right">Total</td>
                                    <td class="text-right"><?php echo number_format($total_default_fee2+$total_default_fee, 2, '.', ',');; ?></td>
                                </tr>

                                </tbody>
                            </table>							
                        </div>
						
                    </div>

                    <hr class="mb-4">

                    <div class="row">
                        <div class="col-md-12">
                            <label class="mb-2 font-weight-bold">Payment History</label>
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Date</th>
                                    <th>Receipt No</th>
                                    <th>Amount (RM)</th>
                                    <th>Remark</th>
                                    <th>Details</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $i = 0;
                                $sql = '
										SELECT * FROM tbl_payment
										WHERE is_delete = 0
										AND branch = "' . branch_now('pid') . '"
										AND student = "' . $id . '"
										ORDER BY create_on
										LIMIT 10
									';
                                $query = $this->db->query($sql)->result_array();
                                $i = 0;
                                foreach ($query as $e)
                                {
                                    $i++;
                                    ?>
                                    <tr>
                                        <td><?php echo $i; ?></td>
                                        <td><?php echo $e['date']; ?></td>
                                        <td><?php echo $e['payment_no']; ?></td>
                                        <td class="text-right"><?php echo number_format($e['total'], 2, '.', ',');; ?></td>
                                        <td><?php echo empty($e['remark']) ? '-' : $e['remark']; ?></td>
                                        <td><a href="<?php echo base_url('payment/edit/' . $e['pid']); ?>"
                                               class="btn btn-primary">Details</a></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <hr class="mb-4">

                    <div class="row">
                        <div class="col-md-6">

                            <div class="form-group row">
                                <label class="col-form-label col-md-3 text-danger">Payment Date</label>
                                <div class="col-md-9">
                                    <input type="date" class="form-control" name="date"
                                           value="<?php echo date('Y-m-d'); ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">Remark</label>
                                <div class="col-md-9">
									<textarea class="form-control" name="remark"
                                              rows="4"><?php foreach ($material_fee as $k => $v)
                                        {
                                            if ($v['fee'] > 0)
                                            {
                                                echo $v['title'] . ' -> RM' . number_format($v['fee'], 2, '.', ',') . "\n";
                                            }
                                        } ?><?php foreach ($subsidy_fee as $k => $v)
                                        {
                                            if ($v['fee'] > 0)
                                            {
                                                echo $v['title'] . ' -> RM' . number_format($v['fee'], 2, '.', ',') . "\n";
                                            }
                                        } ?></textarea>
                                </div>
                            </div>

                        </div>
                        <div class="col-md-6">

                            <div class="form-group row">
                                <label class="col-form-label col-md-3">Payment Method</label>
                                <div class="col-md-9">
                                    <select class="form-control select2" name="payment_method">
                                        <?php foreach ($payment_now as $e) { ?>
                                            <option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
                                        <?php } ?>
                                        <?php foreach ($payment_all as $e) { ?>
                                            <option value="<?php echo $e['secondary']; ?>"><?php echo datalist_Table('tbl_secondary', 'title', $e['secondary']); ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>

                        </div>
                    </div>

                    <table id="item-table" class="table table-bordered mt-3 mb-4">
                        <thead>
                        <th style="width: 3%;">
                            <div class="custom-control custom-checkbox mt-1">
                                <input type="checkbox" class="custom-control-input item-selected" id="checkAll">
                                <label class="custom-control-label" for="checkAll"></label>
                            </div>
                        </th>
                        <th>Item</th>
                        <th class="text-right" style="width: 10%;">Qty</th>
                        <th class="text-right" style="width: 15%;">Unit Price ($)</th>
                        <th class="text-right" style="width: 15%;">Amount ($)</th>
                        </thead>
                        <tbody>
                        <?php

                        $i = 0;

                        if ($std_unpaid_result['count'] > 0)
                        {
                            foreach ($new_unpaid_class as $e)
                            {
                                foreach ($e['data'] as $e2)
                                {
                                    if (isset($e2['amount']))
                                    {
                                        $i++;
                                        ?>
                                        <tr class="item<?php echo $i; ?>">
                                            <td>
                                                <div class="custom-control custom-checkbox mt-1">
                                                    <input type="checkbox"
                                                           class="custom-control-input item-selected class_select_checkbox"
                                                           id="checkbox-<?php echo $i; ?>"
                                                           name="item[<?php echo $i; ?>][selected]" value="1">
                                                    <label class="custom-control-label"
                                                           for="checkbox-<?php echo $i; ?>"></label>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="hidden" class="class_period" value="<?= $e2['period'] ?>">
                                                <!--<input type="hidden" class="class_material_fee" value="= //isset($e2['material_fee']) ? $e2['material_fee'] : 0 ?>">-->
                                                <input type="hidden" class="class_material_fee" value="<?php 
													$t = 0;
													foreach ($with_default_class_bundle as $k => $v)
													{
														$check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
															'parent' => $k,
															'qty' => count($v['data'])
														]);
														
														$t += $check_bundle_price[0]['material'];
													}
													echo $t;
												?>">
												
                                                <input type="hidden" class="childcare_fee"
                                                       value="<?=$student_data['childcare_title'] == "" ? 0 : $student_data['childcare_price'] ?>">
                                                <input type="hidden" class="transport_fee"
                                                       value="<?=$student_data['transport_title'] == "" ? 0 : $student_data['transport_price'] ?>">
                                                <input type="hidden" class="class_subsidy_fee"
                                                       value="<?= isset($e2['subsidy_fee']) ? $e2['subsidy_fee'] : 0 ?>">
                                                <input type="hidden" name="item[<?php echo $i; ?>][type]" value="class">
                                                <input type="hidden" name="item[<?php echo $i; ?>][period]"
                                                       value="<?php echo isset($e2['period']) ? $e2['period'] : ''; ?>">
                                                <input type="hidden" name="item[<?php echo $i; ?>][item]"
                                                       value="<?php echo $e2['class']; ?>">
                                                <input type="hidden" name="item[<?php echo $i; ?>][dis_amount]"
                                                       value="<?php echo $e2['discount']; ?>">
                                                <input type="text" onclick="this.select();" class="form-control"
                                                       name="item[<?php echo $i; ?>][title]"
                                                       value="<?php echo isset($e2['period']) ? '[' . $e2['period'] . '] ' . $e2['title'] : $e2['title']; ?>"
                                                       readonly>
                                                <textarea class="form-control mt-2" onclick="this.select();"
                                                          name="item[<?php echo $i; ?>][remark]" rows="1"></textarea>
                                                <div class="mt-2">
                                                    <a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
                                                       class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
                                                </div>
                                            </td>
                                            <td><input type="number" onclick="this.select();"
                                                       name="item[<?php echo $i; ?>][qty]"
                                                       class="input-remove_arrow form-control text-right"
                                                       value="<?php echo $e2['qty']; ?>" required></td>
                                            <td><input type="number" step="0.01" onclick="this.select();"
                                                       name="item[<?php echo $i; ?>][price_unit]"
                                                       class="input-remove_arrow form-control text-right"
                                                       value="<?php echo number_format($e2['amount'], 2, '.', ''); ?>"
                                                       required></td>
                                            <td><input type="number" step="0.01" onclick="this.select();"
                                                       name="item[<?php echo $i; ?>][amount]"
                                                       class="input-remove_arrow form-control text-right"
                                                       value="<?php echo number_format($e2['amount'], 2, '.', ''); ?>"
                                                       readonly></td>
                                        </tr>
                                        <?php
                                    }
                                }

                                if (isset($e['data']['amount']))
                                {
                                    $i++;
                                    ?>
                                    <tr class="item<?php echo $i; ?>">
                                        <td>
                                            <div class="custom-control custom-checkbox mt-1">
                                                <input type="checkbox" class="custom-control-input item-selected "
                                                       id="checkbox-<?php echo $i; ?>"
                                                       name="item[<?php echo $i; ?>][selected]" value="1">
                                                <label class="custom-control-label"
                                                       for="checkbox-<?php echo $i; ?>"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="hidden" name="item[<?php echo $i; ?>][type]" value="class">
                                            <input type="hidden" name="item[<?php echo $i; ?>][period]"
                                                   value="<?php echo isset($e['data']['period']) ? $e['data']['period'] : ''; ?>">
                                            <input type="hidden" name="item[<?php echo $i; ?>][item]"
                                                   value="<?php echo $e['data']['class']; ?>">
                                            <input type="hidden" name="item[<?php echo $i; ?>][dis_amount]"
                                                   value="<?php echo ($i == 1) ? $e['data']['discount'] + $total_subsidy_fee : $e['data']['discount']; ?>">
                                            <input type="text" onclick="this.select();" class="form-control"
                                                   name="item[<?php echo $i; ?>][title]"
                                                   value="<?php echo isset($e['data']['period']) ? '[' . $e['data']['period'] . '] ' . $e['data']['title'] : $e['data']['title']; ?>"
                                                   readonly>
                                            <textarea class="form-control mt-2" onclick="this.select();"
                                                      name="item[<?php echo $i; ?>][remark]" rows="1"></textarea>
                                            <div class="mt-2">
                                                <a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
                                                   class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
                                            </div>
                                        </td>
                                        <td><input type="number" onclick="this.select();"
                                                   name="item[<?php echo $i; ?>][qty]"
                                                   class="input-remove_arrow form-control text-right"
                                                   value="<?php echo $e['data']['qty']; ?>" required></td>
                                        <td><input type="number" step="0.01" onclick="this.select();"
                                                   name="item[<?php echo $i; ?>][price_unit]"
                                                   class="input-remove_arrow form-control text-right"
                                                   value="<?php echo number_format($e['data']['amount'], 2, '.', ''); ?>"
                                                   required></td>
                                        <td><input type="number" step="0.01" onclick="this.select();"
                                                   name="item[<?php echo $i; ?>][amount]"
                                                   class="input-remove_arrow form-control text-right"
                                                   value="<?php echo number_format($e['data']['amount'], 2, '.', ''); ?>"
                                                   readonly></td>
                                    </tr>
                                    <?php
                                }
                            }

                        }

                        if (isset($std_unpaid_result['result']['item']))
                        {

                            foreach ($std_unpaid_result['result']['item'] as $e)
                            {

                                $i++;

                                ?>
                                <tr class="item<?php echo $i; ?>">
                                    <td>
                                        <div class="custom-control custom-checkbox mt-1">
                                            <input type="checkbox" class="custom-control-input item-selected"
                                                   id="checkbox-<?php echo $i; ?>"
                                                   name="unpaid[<?php echo $i; ?>][selected]" value="1">
                                            <label class="custom-control-label"
                                                   for="checkbox-<?php echo $i; ?>"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="unpaid[<?php echo $i; ?>][id]"
                                               value="<?php echo $e['id']; ?>">
                                        <input type="hidden" name="unpaid[<?php echo $i; ?>][item]"
                                               value="<?php echo $e['item']; ?>">
                                        <input type="hidden" name="unpaid[<?php echo $i; ?>][dis_amount]"
                                               value="<?php echo $e['discount']; ?>">
                                        <input type="hidden" name="unpaid[<?php echo $i; ?>][movement]"
                                               value="<?php echo datalist_Table('log_join', 'movement', $e['id'], 'id'); ?>">
                                        <input type="hidden" name="unpaid[<?php echo $i; ?>][movement_log]"
                                               value="<?php echo datalist_Table('log_join', 'movement_log', $e['id'], 'id'); ?>">
                                        <input type="text" onclick="this.select();" class="form-control"
                                               name="unpaid[<?php echo $i; ?>][title]"
                                               value="<?php echo $e['title']; ?>" readonly>
                                        <textarea class="form-control mt-2" onclick="this.select();"
                                                  name="unpaid[<?php echo $i; ?>][remark]"
                                                  rows="1"><?php echo datalist_Table('log_join', 'remark', $e['id'], 'id'); ?></textarea>
                                        <div class="mt-2">
                                            <a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
                                               class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
                                        </div>
                                    </td>
                                    <td><input type="number" onclick="this.select();"
                                               name="unpaid[<?php echo $i; ?>][qty]"
                                               class="input-remove_arrow form-control text-right"
                                               value="<?php echo $e['qty']; ?>" required></td>
                                    <td><input type="number" step="0.01" onclick="this.select();"
                                               name="unpaid[<?php echo $i; ?>][price_unit]"
                                               class="input-remove_arrow form-control text-right"
                                               value="<?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e['item']), 2, '.', ''); ?>"
                                               required></td>
                                    <td><input type="number" step="0.01" onclick="this.select();"
                                               name="unpaid[<?php echo $i; ?>][amount]"
                                               class="input-remove_arrow form-control text-right"
                                               value="<?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e['item']) * $e['qty'], 2, '.', ''); ?>"
                                               readonly></td>
                                    </td>
                                </tr>
                                <?php

                            }

                        }

                        if (isset($std_unpaid_result['result']['service']))
                        {

                            foreach ($std_unpaid_result['result']['service'] as $e)
                            {

                                $i++;

                                ?>
                                <tr class="item<?php echo $i; ?>">
                                    <td>
                                        <div class="custom-control custom-checkbox mt-1">
                                            <input type="checkbox" class="custom-control-input item-selected"
                                                   id="checkbox-<?php echo $i; ?>"
                                                   name="item[<?php echo $i; ?>][selected]" value="1">
                                            <label class="custom-control-label"
                                                   for="checkbox-<?php echo $i; ?>"></label>
                                        </div>
                                    </td>
                                    <td>
                                        <input type="hidden" name="item[<?php echo $i; ?>][type]" value="service">
                                        <input type="hidden" name="item[<?php echo $i; ?>][period]"
                                               value="<?php echo isset($e['period']) ? $e['period'] : ''; ?>">
                                        <input type="hidden" name="item[<?php echo $i; ?>][item]"
                                               value="<?php echo $e['item']; ?>">
                                        <input type="hidden" name="item[<?php echo $i; ?>][dis_amount]"
                                               value="<?php echo $e['discount']; ?>">
                                        <input type="text" onclick="this.select();" class="form-control"
                                               name="item[<?php echo $i; ?>][title]"
                                               value="<?php echo isset($e['period']) ? '[' . $e['period'] . '] ' . $e['title'] : $e['title']; ?>"
                                               readonly>
                                        <textarea class="form-control mt-2" onclick="this.select();"
                                                  name="item[<?php echo $i; ?>][remark]" rows="1"></textarea>
                                        <div class="mt-2">
                                            <a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
                                               class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
                                        </div>
                                    </td>
                                    <td><input type="number" onclick="this.select();"
                                               name="item[<?php echo $i; ?>][qty]"
                                               class="input-remove_arrow form-control text-right"
                                               value="<?php echo $e['qty']; ?>" required></td>
                                    <td><input type="number" step="0.01" onclick="this.select();"
                                               name="item[<?php echo $i; ?>][price_unit]"
                                               class="input-remove_arrow form-control text-right"
                                               value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" required>
                                    </td>
                                    <td><input type="number" step="0.01" onclick="this.select();"
                                               name="item[<?php echo $i; ?>][amount]"
                                               class="input-remove_arrow form-control text-right"
                                               value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" readonly>
                                    </td>
                                    </td>
                                </tr>
                                <?php

                            }

                        }

                        ?>
						
						<?php

                        if( isset($_GET['next']) ) {
							$std_unpaid_result2 = std_unpaid_result2_2($id);
							// verbose($std_unpaid_result2);
							if ($std_unpaid_result2['count'] > 0)
							{
								foreach ($new_unpaid_class as $e)
								{
									foreach ($e['data'] as $e2)
									{
										if (isset($e2['amount']))
										{
											$i++;
											?>
											<tr class="item<?php echo $i; ?>">
												<td>
													<div class="custom-control custom-checkbox mt-1">
														<input type="checkbox"
															   class="custom-control-input item-selected class_select_checkbox"
															   id="checkbox-<?php echo $i; ?>"
															   name="item[<?php echo $i; ?>][selected]" value="1">
														<label class="custom-control-label"
															   for="checkbox-<?php echo $i; ?>"></label>
													</div>
												</td>
												<td>
													<input type="hidden" class="class_period" value="<?= $e2['period'] ?>">
													<input type="hidden" class="class_material_fee" value="<?php 
													$t = 0;
													foreach ($with_default_class_bundle as $k => $v)
													{
														$check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
															'parent' => $k,
															'qty' => count($v['data'])
														]);
														
														$t += $check_bundle_price[0]['material'];
													}
													echo $t;
												?>">
													<input type="hidden" class="childcare_fee"
														   value="<?=$student_data['childcare_title'] == "" ? 0 : $student_data['childcare_price'] ?>">
													<input type="hidden" class="transport_fee"
														   value="<?=$student_data['transport_title'] == "" ? 0 : $student_data['transport_price'] ?>">
													<input type="hidden" class="class_subsidy_fee"
														   value="<?= isset($e2['subsidy_fee']) ? $e2['subsidy_fee'] : 0 ?>">
													<input type="hidden" name="item[<?php echo $i; ?>][type]" value="class">
													<input type="hidden" name="item[<?php echo $i; ?>][period]"
														   value="<?php echo isset($e2['period']) ? $e2['period'] : ''; ?>">
													<input type="hidden" name="item[<?php echo $i; ?>][item]"
														   value="<?php echo $e2['class']; ?>">
													<input type="hidden" name="item[<?php echo $i; ?>][dis_amount]"
														   value="<?php echo $e2['discount']; ?>">
													<input type="text" onclick="this.select();" class="form-control"
														   name="item[<?php echo $i; ?>][title]"
														   value="<?php echo isset($e2['period']) ? '[' . addOneMonth($e2['period']) . '] ' . $e2['title'] : $e2['title']; ?>"
														   readonly>
													<textarea class="form-control mt-2" onclick="this.select();"
															  name="item[<?php echo $i; ?>][remark]" rows="1"></textarea>
													<div class="mt-2">
														<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
														   class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
													</div>
												</td>
												<td><input type="number" onclick="this.select();"
														   name="item[<?php echo $i; ?>][qty]"
														   class="input-remove_arrow form-control text-right"
														   value="<?php echo $e2['qty']; ?>" required></td>
												<td><input type="number" step="0.01" onclick="this.select();"
														   name="item[<?php echo $i; ?>][price_unit]"
														   class="input-remove_arrow form-control text-right"
														   value="<?php echo number_format($e2['amount'], 2, '.', ''); ?>"
														   required></td>
												<td><input type="number" step="0.01" onclick="this.select();"
														   name="item[<?php echo $i; ?>][amount]"
														   class="input-remove_arrow form-control text-right"
														   value="<?php echo number_format($e2['amount'], 2, '.', ''); ?>"
														   readonly></td>
											</tr>
											<?php
										}
									}

									if (isset($e['data']['amount']))
									{
										$i++;
										?>
										<tr class="item<?php echo $i; ?>">
											<td>
												<div class="custom-control custom-checkbox mt-1">
													<input type="checkbox" class="custom-control-input item-selected "
														   id="checkbox-<?php echo $i; ?>"
														   name="item[<?php echo $i; ?>][selected]" value="1">
													<label class="custom-control-label"
														   for="checkbox-<?php echo $i; ?>"></label>
												</div>
											</td>
											<td>
												<input type="hidden" name="item[<?php echo $i; ?>][type]" value="class">
												<input type="hidden" name="item[<?php echo $i; ?>][period]"
													   value="<?php echo isset($e['data']['period']) ? $e['data']['period'] : ''; ?>">
												<input type="hidden" name="item[<?php echo $i; ?>][item]"
													   value="<?php echo $e['data']['class']; ?>">
												<input type="hidden" name="item[<?php echo $i; ?>][dis_amount]"
													   value="<?php echo ($i == 1) ? $e['data']['discount'] + $total_subsidy_fee : $e['data']['discount']; ?>">
												<input type="text" onclick="this.select();" class="form-control"
													   name="item[<?php echo $i; ?>][title]"
													   value="<?php echo isset($e['data']['period']) ? '[' . $e['data']['period'] . '] ' . $e['data']['title'] : $e['data']['title']; ?>"
													   readonly>
												<textarea class="form-control mt-2" onclick="this.select();"
														  name="item[<?php echo $i; ?>][remark]" rows="1"></textarea>
												<div class="mt-2">
													<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
													   class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
												</div>
											</td>
											<td><input type="number" onclick="this.select();"
													   name="item[<?php echo $i; ?>][qty]"
													   class="input-remove_arrow form-control text-right"
													   value="<?php echo $e['data']['qty']; ?>" required></td>
											<td><input type="number" step="0.01" onclick="this.select();"
													   name="item[<?php echo $i; ?>][price_unit]"
													   class="input-remove_arrow form-control text-right"
													   value="<?php echo number_format($e['data']['amount'], 2, '.', ''); ?>"
													   required></td>
											<td><input type="number" step="0.01" onclick="this.select();"
													   name="item[<?php echo $i; ?>][amount]"
													   class="input-remove_arrow form-control text-right"
													   value="<?php echo number_format($e['data']['amount'], 2, '.', ''); ?>"
													   readonly></td>
										</tr>
										<?php
									}
								}

							}

							if (isset($std_unpaid_result['result']['item']))
							{

								foreach ($std_unpaid_result['result']['item'] as $e)
								{

									$i++;

									?>
									<tr class="item<?php echo $i; ?>">
										<td>
											<div class="custom-control custom-checkbox mt-1">
												<input type="checkbox" class="custom-control-input item-selected"
													   id="checkbox-<?php echo $i; ?>"
													   name="unpaid[<?php echo $i; ?>][selected]" value="1">
												<label class="custom-control-label"
													   for="checkbox-<?php echo $i; ?>"></label>
											</div>
										</td>
										<td>
											<input type="hidden" name="unpaid[<?php echo $i; ?>][id]"
												   value="<?php echo $e['id']; ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][item]"
												   value="<?php echo $e['item']; ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][dis_amount]"
												   value="<?php echo $e['discount']; ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][movement]"
												   value="<?php echo datalist_Table('log_join', 'movement', $e['id'], 'id'); ?>">
											<input type="hidden" name="unpaid[<?php echo $i; ?>][movement_log]"
												   value="<?php echo datalist_Table('log_join', 'movement_log', $e['id'], 'id'); ?>">
											<input type="text" onclick="this.select();" class="form-control"
												   name="unpaid[<?php echo $i; ?>][title]"
												   value="<?php echo $e['title']; ?>" readonly>
											<textarea class="form-control mt-2" onclick="this.select();"
													  name="unpaid[<?php echo $i; ?>][remark]"
													  rows="1"><?php echo datalist_Table('log_join', 'remark', $e['id'], 'id'); ?></textarea>
											<div class="mt-2">
												<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
												   class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
											</div>
										</td>
										<td><input type="number" onclick="this.select();"
												   name="unpaid[<?php echo $i; ?>][qty]"
												   class="input-remove_arrow form-control text-right"
												   value="<?php echo $e['qty']; ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();"
												   name="unpaid[<?php echo $i; ?>][price_unit]"
												   class="input-remove_arrow form-control text-right"
												   value="<?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e['item']), 2, '.', ''); ?>"
												   required></td>
										<td><input type="number" step="0.01" onclick="this.select();"
												   name="unpaid[<?php echo $i; ?>][amount]"
												   class="input-remove_arrow form-control text-right"
												   value="<?php echo number_format(datalist_Table('tbl_inventory', 'price_sale', $e['item']) * $e['qty'], 2, '.', ''); ?>"
												   readonly></td>
										</td>
									</tr>
									<?php

								}

							}

							if (isset($std_unpaid_result['result']['service']))
							{

								foreach ($std_unpaid_result['result']['service'] as $e)
								{

									$i++;

									?>
									<tr class="item<?php echo $i; ?>">
										<td>
											<div class="custom-control custom-checkbox mt-1">
												<input type="checkbox" class="custom-control-input item-selected"
													   id="checkbox-<?php echo $i; ?>"
													   name="item[<?php echo $i; ?>][selected]" value="1">
												<label class="custom-control-label"
													   for="checkbox-<?php echo $i; ?>"></label>
											</div>
										</td>
										<td>
											<input type="hidden" name="item[<?php echo $i; ?>][type]" value="service">
											<input type="hidden" name="item[<?php echo $i; ?>][period]"
												   value="<?php echo isset($e['period']) ? $e['period'] : ''; ?>">
											<input type="hidden" name="item[<?php echo $i; ?>][item]"
												   value="<?php echo $e['item']; ?>">
											<input type="hidden" name="item[<?php echo $i; ?>][dis_amount]"
												   value="<?php echo $e['discount']; ?>">
											<input type="text" onclick="this.select();" class="form-control"
												   name="item[<?php echo $i; ?>][title]"
												   value="<?php echo isset($e['period']) ? '[' . $e['period'] . '] ' . $e['title'] : $e['title']; ?>"
												   readonly>
											<textarea class="form-control mt-2" onclick="this.select();"
													  name="item[<?php echo $i; ?>][remark]" rows="1"></textarea>
											<div class="mt-2">
												<a href="javascript:;" onclick="row_del(<?php echo $i; ?>)"
												   class="text-danger"><i class="fa fa-fw fa-trash"></i> Remove</a>
											</div>
										</td>
										<td><input type="number" onclick="this.select();"
												   name="item[<?php echo $i; ?>][qty]"
												   class="input-remove_arrow form-control text-right"
												   value="<?php echo $e['qty']; ?>" required></td>
										<td><input type="number" step="0.01" onclick="this.select();"
												   name="item[<?php echo $i; ?>][price_unit]"
												   class="input-remove_arrow form-control text-right"
												   value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" required>
										</td>
										<td><input type="number" step="0.01" onclick="this.select();"
												   name="item[<?php echo $i; ?>][amount]"
												   class="input-remove_arrow form-control text-right"
												   value="<?php echo number_format($e['amount'], 2, '.', ''); ?>" readonly>
										</td>
										</td>
									</tr>
									<?php
								}
                            }

                        }

                        ?>
						
                        </tbody>
                        <tfoot>
                        <td colspan="4" class="text-center">
                            <a href="javascript:;" data-toggle="modal" data-target="#modal-add"><i
                                        class="fa fa-fw fa-plus-circle"></i> Add New</a>
                        </td>
                        </tfoot>
                    </table>

					<?php if( isset($_GET['next']) ) {
						$total_material_fee *= 2;
						$student_data['childcare_price'] *= 2;
						$student_data['transport_price'] *= 2;
					}
						?>

                    <div class="row">
                        <div class="col-md-6 offset-md-6">

                            <div class="form-group row">
                                <input type="hidden" name="subtotal"/>
                                <label class="form-control-label col-md-4">Subtotal</label>
                                <label id="subtotal" class="form-control-label col-md-4 offset-md-4 text-right"
                                       data-label="subtotal">0.00</label>
                            </div>

                            <div class="form-group row">
                                <label class="form-control-label col-md-4">Discount</label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="number" step="0.01" onclick="this.select();"
                                               class="input-remove_arrow form-control" name="discount"
                                               value="<?php echo $std_unpaid_result['discount']; ?>" placeholder="0">
                                        <select class="form-control" name="discount_type" required>
                                            <option value="%">%</option>
                                            <option value="$" <?php if ($std_unpaid_result['discount'] > 0) echo 'selected'; ?>>
                                                $
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <label class="form-control-label col-md-4 text-right" data-label="discount">0.00</label>
                            </div>

                            <div class="form-group row">
                                <label class="form-control-label col-md-4">Material Fee</label>
                                <div class="col-md-4">
                                    <input readonly type="number" step="0.01" onclick="this.select();"
                                           class="input-remove_arrow form-control material_fee_txt" name="material_fee"
                                           placeholder="0"
                                           value="<?php if ($total_material_fee > 0) echo $total_material_fee; ?>">
                                </div>
                                <label class="form-control-label col-md-4 text-right"
                                       data-label="material_fee"><?php echo ($total_material_fee > 0) ? number_format($total_material_fee, 2, '.', ',') : '0.00'; ?></label>
                            </div>

                            <? if ($student_data['childcare_title'] != ""): ?>
                                <div class="form-group row">
                                    <label class="form-control-label col-md-4">Childcare Fee</label>
                                    <div class="col-md-4">
                                        <input readonly type="number" step="0.01" onclick="this.select();"
                                               class="input-remove_arrow form-control childcare_fee_text" name="childcare_fee"
                                               placeholder="0"
                                               value="<?=$student_data['childcare_price']?>">
                                    </div>
                                    <label class="form-control-label col-md-4 text-right"
                                           data-label="childcare_fee"><?=number_format($student_data['childcare_price'], 2, '.', ',')?></label>
                                </div>
                            <? else:?>
                                <input  type="hidden" class="childcare_fee_text" name="childcare_fee" value="0">
                            <? endif; ?>

                            <? if ($student_data['transport_title'] != ""): ?>
                                <div class="form-group row">
                                    <label class="form-control-label col-md-4">Transport Fee</label>
                                    <div class="col-md-4">
                                        <input readonly type="number" step="0.01" onclick="this.select();"
                                               class="input-remove_arrow form-control transport_fee_text" name="transport_fee"
                                               placeholder="0"
                                               value="<?=$student_data['transport_price']?>">
                                    </div>
                                    <label class="form-control-label col-md-4 text-right"
                                           data-label="transport_fee"><?=number_format($student_data['transport_price'], 2, '.', ',')?></label>
                                </div>
                            <? else:?>
                                <input  type="hidden" class="transport_fee_text" name="transport_fee" value="0">
                            <? endif; ?>




                            <div class="form-group row">
                                <div class="col-md-4">
                                    <input type="text" id="adjust-title" onclick="this.select();" class="form-control"
                                           name="adjust_label" placeholder="Adjustment" value=""/>
                                </div>
                                <div class="col-md-4">
                                    <input type="number" step="0.01" onclick="this.select();"
                                           class="input-remove_arrow form-control" name="adjust" placeholder="0"
                                           value="">
                                </div>
                                <label class="form-control-label col-md-4 text-right"
                                       data-label="adjust"><?php echo '0.00'; ?></label>
                            </div>

                            <div class="form-group">
                                <div class="alert alert-info d-flex justify-content-between">
                                    <span>Ewallet balance: <b><?php echo user_point('ewallet', $id); ?></b></span>
                                    <input type="hidden" name="ewallet_value"
                                           value="<?php echo user_point('ewallet', $id); ?>">
                                    <div class="form-check">
                                        <input class="form-check-input m-0" type="checkbox" name="ewallet" id="ewallet"
                                               style="height: 16px; width: 16px; top: 50%; transform: translateY(-50%); left: -.3rem;">
                                        <label class="form-check-label" for="ewallet">Use ewallet</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <hr class="my-4"/>
                            </div>

                            <div class="form-group row">
                                <input type="hidden" name="tax-percentage" value="<?php echo branch_now('tax'); ?>"/>
                                <input type="hidden" name="tax"/>
                                <label class="form-control-label col-md-4">Tax</label>
                                <label class="form-control-label col-md-4 offset-md-4 text-right"
                                       data-label="tax">0.00</label>
                            </div>


                            <div class="form-group row">
                                <input type="hidden" id="total" name="total">
                                <input type="hidden" id="price-amount" name="price-amount">
                                <label class="form-control-label col-md-4 font-weight-bold">Total</label>
                                <label class="form-control-label col-md-4 offset-md-4 text-right font-weight-bold"
                                       data-label="total">0.00</label>
                            </div>


                            <div class="form-group row">
                                <label class="form-control-label col-md-4">Receive </label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text" 
                                               class="input-remove_arrow form-control receive_txt" name="receive"
                                               value="" placeholder="0">
                                    </div>
                                </div>
                            </div>


                            <div class="form-group row">
                                <label class="form-control-label col-md-4">Change </label>
                                <div class="col-md-4">
                                    <div class="input-group">
                                        <input type="text"
                                               class="input-remove_arrow form-control change_txt" name="change"
                                               value="0" placeholder="0" readonly>
                                    </div>
                                </div>
                            </div>


                        </div>
                    </div>

                    <hr class="mb-4">

                    <div class="row">
                        <div class="col-md-12 text-left">
								<a href="<?php echo base_url('payment/add/'.$id.'?next=1'); ?>"><button type="button" class="btn btn-primary" >Add next month</button></a>

						</div>
						
                        <div class="col-md-12 text-right">
                            <div class="form-group row">
                                <div class="col-md-9 offset-md-3">
                                    <a href="<?php echo base_url($thispage['group'] . '/list'); ?>"
                                       class="btn btn-link text-muted">Cancel</a>
                                    <button type="submit" name="save_draft" class="btn btn-primary">Save as Draft
                                    </button>
                                    <button type="submit" name="save" class="btn btn-primary">Save</button>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php
                }
                ?>

            </form>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<div class="modal fade" id="modal-add">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                <div class="alert alert-warning mt-2 d-none">Please select and enter the required fields!</div>

                <div class="form-group">
                    <label class="text-danger d-block">Type</label>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="radio-type-1" name="type" class="custom-control-input" value="class"
                               required>
                        <label class="custom-control-label" for="radio-type-1">Class</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="radio-type-2" name="type" class="custom-control-input" value="item"
                               required>
                        <label class="custom-control-label" for="radio-type-2">Item</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="radio-type-3" name="type" class="custom-control-input" value="package"
                               required>
                        <label class="custom-control-label" for="radio-type-3">Package</label>
                    </div>
                </div>

                <form method="post" class="d-none section-dynamic section-class">
                    <div class="form-group">
                        <label class="text-danger">Class</label>
                        <input type="hidden" name="user" value="<?php echo $id; ?>">
                        <input type="hidden" name="title">
                        <input type="hidden" name="id">
                        <select class="form-control select2" name="class" data-required="true">
                            <option value="">-</option>
                            ';
                            <?php foreach ($classes as $e)
                            {
                                echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                            }; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="text-danger">Period</label>
                        <input type="month" class="form-control" name="period" data-required="true"
                               value="<?php echo date('Y-m'); ?>">
                    </div>

                    <div class="form-group">
                        <label>Qty</label>
                        <input type="text" onclick="this.select();" class="form-control" name="class-qty"
                               data-required="true" value="1">
                    </div>

                    <div class="form-group">
                        <label>Unit Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-size: 14px;">$</span>
                            </div>
                            <input type="amount" step="0.01" class="form-control" name="class-price_unit">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-size: 14px;">$</span>
                            </div>
                            <input type="amount" step="0.01" class="form-control" name="class-amount" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remark</label>
                        <textarea class="form-control" onclick="this.select();" name="class-remark" rows="4"></textarea>
                    </div>

                </form>

                <form method="post" class="d-none section-dynamic section-item">
                    <div class="form-group">
                        <label class="d-flex justify-content-between align-items-end">
                            <span class="text-danger">Item</span>
                            <a href="javascript:;" onclick="show_category()" class="small">Filter by category</a>
                        </label>
                        <input type="hidden" name="user" value="<?php echo $id; ?>">
                        <input type="hidden" name="title">
                        <input type="hidden" name="id">
                        <div class="category-sec mb-2 d-none">
                            <hr>
                            <div class="form-group mb-0">
                                <label>Category</label>
                                <select class="form-control select2 category">
                                    <?php foreach ($item_cat as $e) { ?>
                                        <option value="<?php echo $e['pid']; ?>"><?php echo $e['title']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <hr>
                        </div>
                        <select class="form-control select2" name="item" data-required="true">
                            <option value="">-</option>
                            <?php foreach ($item_cat as $e) { ?>
                                <optgroup label="<?php echo $e['title']; ?>">
                                    <?php
                                    $items = $this->tbl_inventory_model->list(branch_now('pid'), 'item', ['active' => 1, 'category' => $e['pid']]);
                                    foreach ($items as $e2)
                                    {
                                        ?>
                                        <option value="<?php echo $e2['pid']; ?>"><?php echo $e2['title']; ?></option>
                                        <?php
                                    }
                                    ?>
                                </optgroup>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Qty</label>
                        <input type="text" class="form-control" onclick="this.select();" name="item-qty"
                               data-required="true" value="1">
                    </div>

                    <div class="form-group">
                        <label>Unit Price</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-size: 14px;">$</span>
                            </div>
                            <input type="amount" step="0.01" class="form-control" name="item-price_unit">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text" style="font-size: 14px;">$</span>
                            </div>
                            <input type="amount" step="0.01" class="form-control" name="item-amount" readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Remark</label>
                        <textarea class="form-control" name="item-remark" rows="4"></textarea>
                    </div>

                </form>

                <form method="post" class="d-none section-dynamic section-package">
                    <div class="form-group">
                        <label class="text-danger">Package</label>
                        <input type="hidden" name="user" value="<?php echo $id; ?>">
                        <input type="hidden" name="title">
                        <input type="hidden" name="id">
                        <select class="form-control select2" name="package" data-required="true">
                            <option value="">-</option>
                            ';
                            <?php foreach ($package as $e)
                            {
                                echo '<option value="' . $e['pid'] . '">' . $e['title'] . '</option>';
                            }; ?>
                        </select>
                    </div>

                </form>

            </div>

            <div class="modal-footer">
                <button type="button" name="add" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>