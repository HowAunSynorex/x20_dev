<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php $this->load->view('inc/navbar', $thispage); ?>

<div id="wrapper" class="toggled">

    <?php $this->load->view('inc/sidebar'); ?>

    <div id="page-content-wrapper">

        <div class="container-fluid py-2">
            <div class="row">
                <div class="col-6 my-auto">
                    <h4 class="py-2 mb-0 font-weight-bold">
                        <?php

                        echo $thispage['title'];

                        if (isset($_GET['class'])) echo '<span class="badge badge-secondary badge-pill badge-sm ml-2"><a href="javascript:;" data-label="return_student" class="text-white"><i class="fa fa-fw fa-times"></i></a> ' . datalist_Table('tbl_classes', 'title', $_GET['class']) . '</span>';
                        if (isset($_GET['parent'])) echo '<span class="badge badge-secondary badge-pill badge-sm ml-2"><a href="javascript:;" data-label="return_student" class="text-white"><i class="fa fa-fw fa-times"></i></a> ' . datalist_Table('tbl_users', 'fullname_en', $_GET['parent']) . '</span>';

                        ?>
                    </h4>
                </div>
                <div class="col-6 my-auto text-right">
                    <?php if (check_module('Students/Create')) { ?>
                        <div class="btn-group">
                            <a href="<?php echo base_url($thispage['group'] . '/add'); ?>" class="btn btn-primary"><i
                                class="fa mr-1 fa-plus-circle"></i> Add New</a>
                            <button type="button" class="btn btn-primary dropdown-toggle dropdown-toggle-split"
                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item d-flex justify-content-between align-items-center"
                                   href="javascript:;" data-toggle="modal" data-target="#modal-apply">
                                    <span>E-form Apply</span>
                                    <i class="fa fa-fw fa-external-link-square-alt"></i>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="container-fluid container-wrapper">

            <?php echo alert_get(); ?>

            <form method="get">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Fullname</label>
                            <div class="col-md-9">
                                <div class="row">
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="fullname_en"
                                               value="<?php if (isset($_GET['fullname_en'])) echo $_GET['fullname_en']; ?>"
                                               placeholder="English">
                                    </div>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="fullname_cn"
                                               value="<?php if (isset($_GET['fullname_cn'])) echo $_GET['fullname_cn']; ?>"
                                               placeholder="中文 (Optional)">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Card ID</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="rfid_cardid"
                                       value="<?php if (isset($_GET['rfid_cardid'])) echo $_GET['rfid_cardid']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Phone</label>
                            <div class="col-md-9">
                                <input type="tel" class="form-control" name="phone"
                                       value="<?php if (isset($_GET['phone'])) echo $_GET['phone']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Code</label>
                            <div class="col-md-9">
                                <input type="text" class="form-control" name="code"
                                       value="<?php if (isset($_GET['code'])) echo $_GET['code']; ?>">
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-3 col-form-label">Form</label>
                            <div class="col-md-9">
                                <select class="form-control select2" name="form">
                                    <option value="">-</option>
                                    <?php
                                    foreach ($form as $e)
                                    {
                                        ?>
                                        <option value="<?php echo $e['pid']; ?>" <?php if (isset($_GET['form'])) {
                                            if ($_GET['form'] == $e['pid']) {
                                                echo 'selected';
                                            }
                                        } ?> ><?php echo $e['title']; ?></option>
                                        <?php
                                    } ?>
                                </select>
                            </div>
                        </div>

                        <!--<div class="form-group row">-->
                        <!--    <label class="col-md-3 col-form-label">Form</label>-->
                        <!--    <div class="col-md-9">-->
                        <!--        <select class="form-control select2" name="active">-->
                        <!--            <option value=""-->
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--</div>-->

                        <div class="form-group row">
                            <div class="col-md-9 offset-md-3">
                                <button type="submit" name="search" value="search" class="btn btn-primary">Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <div class="row">
                <div class="col-md-6">
                    <!-- Tan jing Suan -->
                    <button onclick="pdf_studentcardlist()" class="btn btn-secondary">
                        <i class="fa mr-1 fa-print"></i>
                    </button>
                    <a href="?search=search&active=1" class="btn btn-secondary">
                        Show all Active
                    </a>
                    <a href="?search=search&active=0" class="btn btn-secondary">
                        Show all Inactive
                    </a>
                    <!-- Tan Jing Suan -->
                    <button onclick="excel_studentlist()" class="btn btn-secondary">
                        <i class="fas fa-file-excel"></i>
                    </button>
                </div>
            </div>
            <form method="post" action="<?php echo base_url('students/bulk'); ?>" class="mb-0">
                <div class="row">
                    <div class="col-md-6">
                        <p class="d-inline-block mb-2">Total Student(s): <?php echo $total_count; ?></p>
                    </div>
                    <div class="col-md-6">
                        <?= $pagination; ?>
                    </div>
                </div>

                <div class="table-responsive">
                    <!-- Tan Jing Suan -->
                    <!-- <table class="table table-hover border"> -->
                    <table class="table table-hover border" id="tbl_students" >
                        <thead>
                            <!-- Tan Jing Suan -->
                            <th style="width: 3%;">
                                <div class="custom-control custom-checkbox mt-1">
                                    <input type="checkbox" class="custom-control-input item-selected" id="checkAll">
                                    <label class="custom-control-label" for="checkAll"></label>
                                </div>
                            </th>
                            <th style="width:7%">No</th>
                            <th style="width:10%">
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=u.code&order=<?= ($_GET['sort'] == 'u.code' && $_GET['order'] == 'ASC') == 'ASC' ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Code <?= $_GET['sort'] == 'u.code' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th>
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=forms.title&order=<?= ($_GET['sort'] == 'forms.title' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Form <?= $_GET['sort'] == 'forms.title' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th style="width:20%">
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=u.fullname_en&order=<?= ($_GET['sort'] == 'u.fullname_en' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Name <?= $_GET['sort'] == 'u.fullname_en' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th style="width:10%">
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=u.active&order=<?= ($_GET['sort'] == 'u.active' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Status <?= $_GET['sort'] == 'u.active' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th>
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=u.gender&order=<?= ($_GET['sort'] == 'u.gender' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Gender <?= $_GET['sort'] == 'u.gender' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th>
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=u.date_join&order=<?= ($_GET['sort'] == 'u.date_join' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Join Date <?= $_GET['sort'] == 'u.date_join' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th>
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=u.phone&order=<?= ($_GET['sort'] == 'u.phone' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">Phone <?= $_GET['sort'] == 'u.phone' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th>
                                <a href='<?= base_url('students/list') . ($thispage['type'] == 'student' ? '' : '/' . $thispage['type']) ?>?per_page=<?= $_GET['per_page'] ?>&sort=schools.title&order=<?= ($_GET['sort'] == 'schools.title' && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?>'>
                                    <span class="text-nowrap">School <?= $_GET['sort'] == 'schools.title' ? ($_GET['order'] == 'ASC' ? '<i class="fas fa-sort-up"></i>' : '<i class="fas fa-sort-down"></i>') : '' ?></span>
                                </a>
                            </th>
                            <th>Joined Class</th>
                            <th>Parent</th>
                            <th>Total Payment</th>
                            <th>Total Discount</th>
                            <th>Total Receivable</th>
                        </thead>
                        <tbody>

                        <?php

                        $counter = $row;
                        foreach ($result as $e)
                        {
                            $counter++;

                            $std_unpaid_result = std_unpaid_result2($e['pid']);
                            $std_default_result = std_default_result($e['pid']);


                            $new_unpaid_class = [];
                            $material_fee = [];
                            $subsidy_fee = [];
                            $with_class_bundle = [];
                            $without_class_bundle = [];
                            $total_material_fee = 0;
                            $total_subsidy_fee = 0;

                            $default_material_fee = [];
                            $default_subsidy_fee = [];
                            $with_default_class_bundle = [];
                            $without_default_class_bundle = [];
                            $default_total_material_fee = 0;
                            $default_total_subsidy_fee = 0;

                            //get default class data (not check payment make or not)
                            if (isset($std_default_result['result']['default_class']))
                            {
                                //$std_unpaid_result['result']['default_class_items'] = search($std_unpaid_result['result']['default_class_items'], 'period', date('Y-m'));
                                foreach ($std_default_result['result']['default_class'] as $e2)
                                {

                                    $check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
                                        'course' => datalist_Table('tbl_classes', 'course', $e2['class'])
                                    ]);
                                    if (count($check_class_bunlde) > 0)
                                    {
                                        $check_class_bunlde = $check_class_bunlde[0];
                                        $with_default_class_bundle[$check_class_bunlde['parent']]['data'][] = $e2;
                                    }
                                    else
                                    {
                                        $without_default_class_bundle[]['data'] = $e2;
                                    }
                                }

                                foreach ($with_default_class_bundle as $k => $v)
                                {
                                    $check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
                                        'parent' => $k,
                                        'qty' => count($v['data'])
                                    ]);
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
                                        for ($i = 0; $i < floor(count($v['data']) / $bundle_subsidy['qty']); $i++)
                                        {
                                            $default_subsidy_fee[] = [
                                                'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                                'fee' => $bundle_subsidy['subsidy']
                                            ];
                                        }
                                    }
                                }

                                foreach ($default_material_fee as $e2)
                                {
                                    $default_total_material_fee += $e2['fee'];
                                }

                                foreach ($default_subsidy_fee as $e2)
                                {
                                    $default_total_subsidy_fee += $e2['fee'];
                                }
                            }

                            $total_default_payment = 0;
                            $total_default_discount = 0;
                            $total_default = 0;
                            $new_default_unpaid_class = array_merge($with_default_class_bundle, $without_default_class_bundle);
                            foreach ($new_default_unpaid_class as $e2)
                            {
                                foreach ($e2['data'] as $e3)
                                {
                                    if (isset($e3['amount']))
                                    {
                                        //$total_default_payment += $e3['amount'] - $e3['discount'];
                                        $total_default_payment += $e3['amount'];
                                        $total_default_discount += $e3['discount'];
                                    }
                                }
                                if (isset($e2['data']['amount']))
                                {
                                   // $total_default_payment += $e2['data']['amount'] - $e2['data']['discount'];
                                    $total_default_payment += $e2['data']['amount'] ;
                                    $total_default_discount += $e2['data']['discount'];
                                }
                            }


                            if (isset($std_default_result['result']['default_item']))
                            {

                                $total_default_payment += array_sum(array_column($std_default_result['result']['default_item'], 'amount'));
                                $total_default_discount += array_sum(array_column($std_default_result['result']['default_item'], 'discount'));

                            }

                            if (isset($std_default_result['result']['default_service']))
                            {

                                $std_default_result['result']['service'] = search($std_default_result['result']['default_service'], 'period', date('Y-m'));

                                $total_default_payment += array_sum(array_column($std_default_result['result']['default_service'], 'amount'));
                                $total_default_discount += array_sum(array_column($std_default_result['result']['default_service'], 'discount'));

                                /* foreach($std_unpaid_result['result']['service'] as $e2) {
                                    $total_payment += $e2['amount'];
                                    $total_discount += $e2['discount'];
                                } */
                            }


                            $student_data = $this->tbl_users_model->view($e['pid'])[0];
                            $childcare_fee = 0;
                            $transport_fee = 0;
                            if ($student_data['childcare_title'] != "")
                                $childcare_fee = $student_data['childcare_price'];

                            if ($student_data['transport_title'] != "")
                                $transport_fee = $student_data['transport_price'];


                            $total_default_payment += $default_total_material_fee;
                            $total_default_payment += $transport_fee;
                            $total_default_payment += $childcare_fee;
                            $total_default_discount += $default_total_subsidy_fee;
                            $total_default = $total_default_payment - $total_default_discount;









                            if ($std_unpaid_result['count'] > 0)
                            {

                                if (isset($std_unpaid_result['result']['class']))
                                {

                                    $std_unpaid_result['result']['class'] = search($std_unpaid_result['result']['class'], 'period', date('Y-m'));

                                    foreach ($std_unpaid_result['result']['class'] as $e2)
                                    {

                                        $check_class_bunlde = $this->log_join_model->list('class_bundle_course', branch_now('pid'), [
                                            'course' => datalist_Table('tbl_classes', 'course', $e2['class'])
                                        ]);
                                        if (count($check_class_bunlde) > 0)
                                        {
                                            $check_class_bunlde = $check_class_bunlde[0];
                                            $with_class_bundle[$check_class_bunlde['parent']]['data'][] = $e2;
                                        }
                                        else
                                        {
                                            $without_class_bundle[]['data'] = $e2;
                                        }
                                    }

                                    foreach ($with_class_bundle as $k => $v)
                                    {
                                        $check_bundle_price = $this->log_join_model->list('class_bundle_price', branch_now('pid'), [
                                            'parent' => $k,
                                            'qty' => count($v['data'])
                                        ]);
                                        if (count($check_bundle_price) > 0)
                                        {
                                            $check_bundle_price = $check_bundle_price[0];
                                            $each_price = $check_bundle_price['amount'] / $check_bundle_price['qty'];
                                            $material_fee[] = [
                                                'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                                'fee' => $check_bundle_price['material']
                                            ];
                                            foreach ($v['data'] as $k2 => $v2)
                                            {
                                                $class_price = datalist_Table('tbl_classes', 'fee', $v2['class']);
                                                $with_class_bundle[$k]['data'][$k2]['discount'] = $class_price - $each_price;
                                                $with_class_bundle[$k]['data'][$k2]['amount'] = $class_price;
                                                $with_class_bundle[$k]['data'][$k2]['title'] = $v2['title'] . ' [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']';
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
                                                    $material_fee[] = [
                                                        'title' => 'Material Fee [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                                        'fee' => $check_bundle_price['material']
                                                    ];
                                                }
                                                foreach ($v['data'] as $k2 => $v2)
                                                {
                                                    if ($k2 >= $check_bundle_price['qty'] * floor(count($v['data']) / $check_bundle_price['qty']))
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
                                                    }
                                                }
                                            }
                                        }
                                    }

                                    foreach ($with_class_bundle as $k => $v)
                                    {
                                        $bundle_subsidy = $this->db->query('
												SELECT * FROM log_join
												WHERE is_delete = 0
												AND type = "class_bundle_price"
												AND branch = "' . branch_now('pid') . '"
												AND parent = "' . $k . '"
												AND subsidy > 0
												AND qty < ' . count($v['data']) . '
												ORDER BY qty DESC
											')->result_array();
                                        if (count($bundle_subsidy) > 0)
                                        {
                                            $bundle_subsidy = $bundle_subsidy[0];
                                            for ($i = 0; $i < floor(count($v['data']) / $bundle_subsidy['qty']); $i++)
                                            {
                                                $subsidy_fee[] = [
                                                    'title' => 'Subsidy [Class Bundle: ' . datalist_Table('tbl_secondary', 'title', $k) . ']',
                                                    'fee' => $bundle_subsidy['subsidy']
                                                ];
                                            }
                                        }
                                    }

                                    foreach ($material_fee as $e2)
                                    {
                                        $total_material_fee += $e2['fee'];
                                    }

                                    foreach ($subsidy_fee as $e2)
                                    {
                                        $total_subsidy_fee += $e2['fee'];
                                    }
                                }
                            }

                            $total_payment = 0;
                            $total_discount = 0;
                            $total = 0;

                            $new_unpaid_class = array_merge($with_class_bundle, $without_class_bundle);
                            foreach ($new_unpaid_class as $e2)
                            {
                                foreach ($e2['data'] as $e3)
                                {
                                    if (isset($e3['amount']))
                                    {
                                        //$total_payment += $e3['amount'] - $e3['discount'];
                                        $total_payment += $e3['amount'];
                                        $total_discount += $e3['discount'];
                                    }
                                }

                                if (isset($e2['data']['amount']))
                                {
                                    //$total_payment += $e2['data']['amount'] - $e2['data']['discount'];
                                    $total_payment += $e2['data']['amount'];
                                    $total_discount += $e2['data']['discount'];
                                }
                            }

                           /*
                            if (isset($std_unpaid_result['result']['item']))
                            {

                                $total_payment += array_sum(array_column($std_unpaid_result['result']['item'], 'amount'));
                                $total_discount += array_sum(array_column($std_unpaid_result['result']['item'], 'discount'));

                            }


                            if (isset($std_unpaid_result['result']['service']))
                            {

                                $std_unpaid_result['result']['service'] = search($std_unpaid_result['result']['service'], 'period', date('Y-m'));

                                $total_payment += array_sum(array_column($std_unpaid_result['result']['service'], 'amount'));
                                $total_discount += array_sum(array_column($std_unpaid_result['result']['service'], 'discount'));


                            }
                            */

                            $total_payment += $total_material_fee;
                            $total_discount += $total_subsidy_fee;
                            $total = $total_payment - $total_discount;

                            ?>
                            <tr>
                                <?php if (isset($_GET['class'])) { ?>
                                    <!-- Tan Jing Suan -->
                                    <td>
                                        <div class="custom-control custom-checkbox mt-1">
                                            <input type="checkbox"
                                                   class="custom-control-input item-selected class_select_checkbox"
                                                   id="checkbox-<?php echo $counter; ?>"
                                                   name="item[<?php echo $counter; ?>][selected]" value="1">
                                            <label class="custom-control-label"
                                                   for="checkbox-<?php echo $counter; ?>"></label>
                                        </div>
                                    </td>
                                    <td><?php echo $counter; ?></td>
                                    <td><?php echo $e['code']; ?></td>
                                    <td><?php echo empty($e['form']) ? '-' : $e['form_title']; ?></td>
                                    <td>
                                        <div class="media">
                                            <?php if (check_module('Students/Update')) { ?>
                                            <a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
                                                <?php } ?>

                                                <img src="<?php echo pointoapi_UploadSource($e['image']); ?>"
                                                     class="mr-2 rounded-circle border"
                                                     style="height: 85px; width: 85px; object-fit: cover">

                                                <?php if (check_module('Students/Update')) { ?>
                                            </a>
                                        <?php } ?>
                                            <div class="media-body my-auto">

                                                <?php if (check_module('Students/Update')) { ?>
                                                <a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
                                                    <?php } ?>
                                                    <?php echo $e['fullname_en'] . ' ' . $e['fullname_cn']; ?>
                                                    <?php if (check_module('Students/Update')) { ?>
                                                </a>
                                            <?php } ?>
                                                <div style="font-size: 1.25rem">
                                                    <a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'https://wa.me/' . wa_format($e['phone']) . '" target="_blank'; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="WhatsApp">
                                                        <i class="fab fa-fw fa-whatsapp"></i>
                                                    </a>
                                                    <a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'tel:' . $e['phone']; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="Call">
                                                        <i class="fa fa-fw fa-phone"></i>
                                                    </a>
                                                    <a href="<?php echo (empty($e['email'])) ? 'javascript:;" style="opacity: .5;' : 'mailto:' . $e['email'] . '" target="_blank'; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="Email">
                                                        <i class="fa fa-fw fa-envelope"></i>
                                                    </a>
                                                    <a href="<?php echo (empty($e['address'])) ? 'javascript:;" style="opacity: .5;' : 'https://maps.google.com/?daddr=' . urlencode($e['address']) . '" target="_blank'; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="Map">
                                                        <i class="fa fa-fw fa-map-marker-alt"></i>
                                                    </a>
                                                    <!-- Tan Jing Suan -->
                                                    <a href="<?php echo 'https://system.synorex.space/highpeakedu/export/pdf_studentcardlist/'.$e['pid']; ?>" style="opacity: .5;" target="_blank"
                                                       class="text-muted" data-toggle="tooltip" title="Print">
                                                        <i class="fa mr-1 fa-print"></i>
                                                    </a>
                                                    <input type="hidden" class="class_user" name="user[<?php echo $counter; ?>]" value="<?=$e['pid']; ?>">
                                                    <input type="hidden" class="class_form" name="form[<?php echo $counter; ?>]" value="<?=$e['form_title']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo badge($e['active']); ?></td>
                                    <td><?php echo ucfirst($e['gender']); ?></td>
                                    <td><?php echo empty($e['date_join']) ? '-' : $e['date_join']; ?></td>
                                    <td><?php echo empty($e['phone']) ? '-' : $e['phone']; ?></td>
                                    <td><?php echo empty($e['school']) ? '-' : $e['school_title']; ?></td>
                                    <td><?php echo empty($e['parent']) ? '-' : datalist_Table('tbl_users', 'fullname_en', $e['parent']); ?></td>
                                    <td>-</td>
                                <?php } else { ?>
                                    <!-- Tan Jing Suan -->
                                    <td>
                                        <div class="custom-control custom-checkbox mt-1">
                                            <input type="checkbox"
                                                   class="custom-control-input item-selected class_select_checkbox"
                                                   id="checkbox-<?php echo $counter; ?>"
                                                   name="item[<?php echo $counter; ?>][selected]" value="1">
                                            <label class="custom-control-label"
                                                   for="checkbox-<?php echo $counter; ?>"></label>
                                        </div>
                                    </td>
                                    <td class="hover-hide-noA">
                                        <span class="no"><?php echo $counter; ?></span>
                                        <?php /*<input type="checkbox" class="checkbox-bulk" name="bulk[]" value="<?php echo $e['pid']; ?>">*/ ?>
                                    </td>
                                    <td><?php echo $e['code']; ?></td>
                                    <td><?php echo empty($e['form']) ? '-' : $e['form_title']; ?></td>
                                    <td>
                                        <div class="media">
                                            <?php if (check_module('Students/Update')) { ?>
                                            <a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
                                                <?php } ?>
                                                <img src="<?php echo pointoapi_UploadSource($e['image']); ?>"
                                                     class="mr-2 rounded-circle border"
                                                     style="height: 85px; width: 85px; object-fit: cover">
                                                <?php if (check_module('Students/Update')) { ?>
                                            </a>
                                        <?php } ?>

                                            <div class="media-body my-auto">

                                                <?php if (check_module('Students/Update')) { ?>
                                                <a href="<?php echo base_url($thispage['group'] . '/edit/' . $e['pid']); ?>">
                                                    <?php } ?>
                                                    <?php echo $e['fullname_en'] . ' ' . $e['fullname_cn']; ?>
                                                    <?php if (check_module('Students/Update')) { ?>
                                                </a>
                                            <?php } ?>
                                                <div style="font-size: 1.25rem">
                                                    <a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'https://wa.me/' . wa_format($e['phone']) . '" target="_blank'; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="WhatsApp">
                                                        <i class="fab fa-fw fa-whatsapp"></i>
                                                    </a>
                                                    <a href="<?php echo (empty($e['phone'])) ? 'javascript:;" style="opacity: .5;' : 'tel:' . $e['phone']; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="Call">
                                                        <i class="fa fa-fw fa-phone"></i>
                                                    </a>
                                                    <a href="<?php echo (empty($e['email'])) ? 'javascript:;" style="opacity: .5;' : 'mailto:' . $e['email'] . '" target="_blank'; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="Email">
                                                        <i class="fa fa-fw fa-envelope"></i>
                                                    </a>
                                                    <a href="<?php echo (empty($e['address'])) ? 'javascript:;" style="opacity: .5;' : 'https://maps.google.com/?daddr=' . urlencode($e['address']) . '" target="_blank'; ?>"
                                                       class="text-muted" data-toggle="tooltip" title="Map">
                                                        <i class="fa fa-fw fa-map-marker-alt"></i>
                                                    </a>
                                                    <!-- Tan Jing Suan -->
                                                    <a href="<?php echo 'https://system.synorex.space/highpeakedu/export/pdf_studentcardlist/'.$e['pid']; ?>" style="opacity: .5;" target="_blank"
                                                       class="text-muted" data-toggle="tooltip" title="Print">
                                                        <i class="fa mr-1 fa-print"></i>
                                                    </a>
                                                    <input type="hidden" class="class_user" name="user[<?php echo $counter; ?>]" value="<?=$e['pid']; ?>">
                                                    <input type="hidden" class="class_form" name="form[<?php echo $counter; ?>]" value="<?=$e['form_title']; ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?php echo badge($e['active']); ?></td>
                                    <td><?php echo ucfirst($e['gender']); ?></td>
                                    <td><?php echo empty($e['date_join']) ? '-' : $e['date_join']; ?></td>
                                    <td>
                                        <?php

                                        if (!empty($e['phone'])) echo $e['phone'] . '<br>';
                                        if (!empty($e['phone2'])) echo $e['phone2'] . '<br>';
                                        if (!empty($e['phone3'])) echo $e['phone3'] . '<br>';
                                        ?>
                                    </td>
                                    <td><?php echo empty($e['school']) ? '-' : $e['school_title']; ?></td>
                                    <td><?php echo $e['join_class_title']; ?></td>
                                    <td>
                                        <?php echo $e['parent_fullname_en']; ?>
                                        <?php
                                        /* $parent = $this->log_join_model->list('join_parent', branch_now('pid'), [ 'user' => $e['pid'], 'active' => 1 ]);
                                        if(count($parent) > 0) {
                                            foreach($parent as $p) {
                                                echo datalist_Table('tbl_users', 'fullname_en', $p['parent']).'<br>';
                                            }
                                        } else {
                                            echo '-';
                                        } */
                                        ?>
                                    </td>

                                    <td class="text-right"><?php echo number_format($total_default_payment, 2, '.', ','); ?></td>
                                    <td class="text-right"><?php echo number_format($total_default_discount, 2, '.', ','); ?></td>
                                    <td class="text-right"><?php echo number_format($total_default, 2, '.', ','); ?></td>


                                <?php } ?>
                            </tr>
                            <?php
                        }
                        ?>

                        </tbody>
                    </table>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <p class="d-inline-block mb-2">Total Student(s): <?php echo $total_count; ?></p>
                    </div>
                    <div class="col-md-6 text-right">
                        <?= $pagination; ?>
                    </div>
                </div>


            </form>

        </div>

        <?php $this->load->view('inc/copyright'); ?>

    </div>

</div>

<form class="modal fade" id="modal-apply">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="staticBackdropLabel">Application E-form</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <a href="<?php echo base_url("landing/apply_form/" . branch_now('pid')); ?>" target="_blank">
                    <img src="https://chart.googleapis.com/chart?chs=150x150&cht=qr&chl=<?php echo base_url("landing/apply_form/" . branch_now('pid')); ?>&choe=UTF-8"
                         class="w-50">
                </a>
                <?php if (branch_now('pointoapi_key') == '') { ?>
                    <div class="alert alert-warning">
                        Setup PointoAPI API Key to use this function. <a
                                href="<?php echo base_url('settings/pointoapi'); ?>" class="text-primary">Setup</a>
                    </div>
                <?php } ?>
                <div class="card mb-2">
                    <div class="card-body">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="english" name="language" class="custom-control-input"
                                   value="english" checked>
                            <label class="custom-control-label" for="english">English</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="chinese" name="language" class="custom-control-input"
                                   value="chinese">
                            <label class="custom-control-label" for="chinese">中文</label>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body align-items-center d-flex justify-content-center">
                        <span class="mr-2">Upload receipt in E-form page</span>
                        <label class="switch">
                            <input type="checkbox"
                                   class="receipt-switch" <?php if (branch_now('pointoapi_key') == '') echo 'disabled'; ?>>
                            <span class="slider round"></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<script>var branch = "<?php echo branch_now('pid'); ?>"; </script>