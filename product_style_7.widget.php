<?php
class widget_product_style_7 extends widget {
    function __construct() {
        parent::__construct('widget_product_style_7', 'Sản phẩm (style 7)', ['container' => true, 'position'  => 'right']);
        add_action('theme_custom_css', array( $this, 'css'), 10);
    }
    function form( $left = [], $right = []) {
        $left[] = ['field'=> 'pr_cate_sub', 'label' =>'Danh mục sản phẩm', 'type' => 'select2-multiple', 'options' => ProductCategory::gets(array('mutilevel' => 'option'))];
        $left[] = [
            'field' => 'pr_status',
            'type' => 'select',
            'options' => ['Sản phẩm mới','Sản phẩm yêu thích','Sản phẩm bán chạy','Sản phẩm nổi bật','Sản phẩm khuyến mãi',],
            'after' => '<div class="col-md-6 form-group group" id="box_pr_status"><label for="pr_status" class="control-label">Loại sản phẩm</label>', 'before'=> '</div>'
        ];
        $left[] = [
            'field' => 'limit',
            'type' => 'number', 'value' => 10,
            'note'=>'Để 0 để lấy tất cả (không khuyên dùng)',
            'after' => '<div class="col-md-6 form-group group"><label for="pr_margin" class="control-label">Số sản phẩm lấy ra</label>', 'before'=> '</div>'
        ];
        $left[] = ['field' => 'pr_display', 'type' => 'product_display_style_7'];
        $right[] = ['field' => 'pr_per_row', 		'label' =>'Số sản phẩm trên 1 hàng', 			'type' => 'col', 'value' => 4, 'args' => array('min'=>1, 'max' => 5)];
        $right[] = ['field' => 'pr_per_row_tablet','label' =>'Số sản phẩm trên 1 hàng - tablet', 	'type' => 'col', 'value' => 3, 'args' => array('min'=>1, 'max' => 5)];
        $right[] = ['field' => 'pr_per_row_mobile','label' =>'Số sản phẩm trên 1 hàng - mobile', 	'type' => 'col', 'value' => 2, 'args' => array('min'=>1, 'max' => 5)];
        parent::form($left, $right);
    }
    function widget($option) {
        $slug           = Url::permalink(URL_PRODUCT);
        $sub_categories = [];
        $main_category  = 0;
        if(have_posts($option->pr_cate_sub) ) {
            $sub_categories = ProductCategory::gets(['where_in' => ['field' => 'id', 'data' => $option->pr_cate_sub]]);
            if(have_posts($sub_categories)) {
                $main_category = $sub_categories[0]->id;
            }
        }
        $box = $this->container_box('widget_product_style_7', $option);
        echo $box['before'];
        ?>
        <div class="product_style_7_header" id="product_style_7_header_<?= $this->id;?>">
            <div class="header-title"><h3 class="header"><span><?= $this->name;?></span></h3></div>
            <div class="text-center">
                <ul class="product_style_7_category_list">
                    <?php foreach ($sub_categories as $sub): ?>
                        <li class="item"><a href="<?php echo Url::permalink($sub->slug);?>" data-wg="<?php echo $this->id;?>" data-id="<?php echo $sub->id;?>" class="<?php echo ($main_category == $sub->id) ? 'active' : '';?>"><?php echo $sub->name;?></a></li>
                    <?php endforeach ?>
                </ul>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="product_style_7_content">
            <div class="box-content product-slider-horizontal" id="widget_product_content_<?= $this->id;?>" style="position: relative">
                <?php $this->loading();?>
                <?php if($option->pr_display['type'] == 0) { ?>
                    <div class="arrow_box">
                        <div id="widget_product_btn_prev_<?= $this->id;?>" class="prev arrow"><i class="fal fa-chevron-left"></i></div>
                        <div id="widget_product_btn_next_<?= $this->id;?>" class="next arrow"><i class="fal fa-chevron-right"></i></div>
                    </div>
                    <div id="product_style_7_content_<?= $this->id;?>" class="owl-carousel list-product"></div>
                    <style>
                        .product-slider-horizontal#widget_product_content_<?= $this->id;?> .item { margin-right: <?php echo $option->pr_display['margin']/2;?>px; margin-left: <?php echo $option->pr_display['margin']/2;?>px;}
                    </style>
                    <?php
                }
                if($option->pr_display['type'] == 1) { ?>
                    <div id="product_style_7_content_<?= $this->id;?>" class="list-product"></div>
                    <?php
                }
                $this->script($option, $main_category);
                ?>
                <div class="text-center" id="product_style_7_morelink_<?= $this->id;?>">
                    <a href="<?= $slug;?>" class="btn btn-white btn-effect-default more-link"><?php echo __('Xem tất cả');?></a>
                </div>
            </div>
        </div>
        <div class="clearfix"></div>
        <?php
        echo $box['after'];
    }
    function displayHorizontal($products, $rows) {
        if($rows == 1) {
            foreach ($products as $key => $val):
                echo scmc_template('loop/item_product', array('val' => $val));
            endforeach;
        }
        if($rows == 2) {
            $row_key = 0;
            foreach ($products as $key => $val):
                if($row_key == 0) echo '<div class="item_product_row">';
                echo scmc_template('loop/item_product', array('val' => $val));
                $row_key++;
                if($row_key == 2) { echo '</div>'; $row_key = 0; }
            endforeach;
            if($row_key < 2) echo '</div>';
        }
    }
    function displayList($wg_products, $option) {
        $option->pr_per_row_mobile = ($option->pr_per_row_mobile == 5)?15:(12/$option->pr_per_row_mobile);
        $option->pr_per_row_tablet = ($option->pr_per_row_tablet == 5)?15:(12/$option->pr_per_row_tablet);
        $option->pr_per_row        = ($option->pr_per_row == 5)?15:(12/$option->pr_per_row);
        ?>
        <div id="widget_product_<?= $this->id;?>" class="list-item-product row">
            <?php foreach ($wg_products as $key => $val): ?>
                <div class="col-xs-<?php echo $option->pr_per_row_mobile;?> col-sm-<?php echo $option->pr_per_row_tablet;?> col-md-<?php echo $option->pr_per_row;?> col-lg-<?php echo $option->pr_per_row;?>">
                    <?php echo scmc_template('loop/item_product', array('val' =>$val));?>
                </div>
            <?php endforeach ?>
        </div>
        <?php
    }
    function loading() {
        ?>
        <div class="wg-loading text-center" style="display:none;">
            <div class="row">
                <div class="col-xs-6 col-sm-4 col-md-15">
                    <?php $this->itemLoading();?>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-15">
                    <?php $this->itemLoading();?>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-15 product--item-load-desktop">
                    <?php $this->itemLoading();?>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-15 product--item-load-tablet">
                    <?php $this->itemLoading();?>
                </div>
                <div class="col-xs-6 col-sm-4 col-md-15 product--item-load-tablet">
                    <?php $this->itemLoading();?>
                </div>
            </div>
        </div>
        <?php
    }
    function itemLoading() {
        ?>
        <div class="product--item-load">
            <div class="picture"></div>
            <div class="row">
                <div class="col-xs-6 col-sm-6 col-md-6 big"></div>
                <div class="col-xs-4 col-sm-4 col-md-4 empty big"></div>
                <div class="col-xs-2 col-sm-2 col-md-2 big"></div>
                <div class="col-xs-4 col-sm-4 col-md-4"></div>
                <div class="col-xs-8 col-sm-8 col-md-8 empty"></div>
                <div class="col-xs-6 col-sm-6 col-md-6"></div>
                <div class="col-xs-6 col-sm-6 col-md-6 empty"></div>
                <div class="col-xs-12 col-sm-12 col-md-12"></div>
            </div>
        </div>
        <?php
    }
    function script($option, $main_category) {
        ?>
        <script defer>
            $(function(){
                $.ajaxSetup({
                    beforeSend: function(xhr, settings) {
                        if (settings.data.indexOf('csrf_test_name') === -1) {
                            settings.data += '&csrf_test_name=' + encodeURIComponent(getCookie('csrf_cookie_name'));
                        }
                    }
                });
                let runSlick = false, categoryId, widgetId , data_item = [], pr_display = <?php echo $option->pr_display['type'];?>;
                let pr_config_style_7 = {
                    infinite: true,
                    dots:false,
                    autoplay: true,
                    autoplaySpeed: <?= $option->pr_display['time']*1000;?>,
                    speed: <?= $option->pr_display['speed']*1000;?>,
                    slidesToShow: <?= $option->pr_per_row;?>,
                    slidesToScroll: <?= $option->pr_per_row;?>,
                    responsive: [
                        { breakpoint: 1000, settings: { slidesToShow: <?= $option->pr_per_row_tablet;?>, slidesToScroll: <?= $option->pr_per_row_tablet;?>, } },
                        { breakpoint: 600, settings: { slidesToShow: <?= $option->pr_per_row_mobile;?>, slidesToScroll: <?= $option->pr_per_row_mobile;?>, } }
                    ]
                };
                function product_style_7_slider(id, config) {
                    if(pr_display === 1) return false;
                    let productList     = $('#product_style_7_content_' + id);
                    let productBtnNext  = $('#widget_product_btn_next_' + id);
                    let productBtnPrev  = $('#widget_product_btn_prev_' + id);
                    productList.slick(config);
                    productBtnNext.click(function() {productList.slick('slickNext');return false;});
                    productBtnPrev.click(function() {productList.slick('slickPrev');return false;});
                    runSlick = true;
                }
                function product_style_7_slider_load(categoryId, widgetId, config) {

                    let product_style_7_content = $('#product_style_7_content_' + widgetId);

                    let product_style_7_link = $('#product_style_7_morelink_' + widgetId+' a.more-link');

                    let loading = $('#widget_product_content_' + widgetId + ' .wg-loading');

                    if(runSlick === true) product_style_7_content.slick('unslick');

                    product_style_7_content.html('');

                    loading.show();

                    if (typeof data_item[categoryId] != 'undefined') {
                        product_style_7_content.html(data_item[categoryId].item);
                        product_style_7_link.attr('href', data_item[categoryId].url);
                        loading.hide();
                        product_style_7_slider(widgetId, config);
                        return false;
                    }
                    else {
                        let data = {
                            action  : 'widget_product_style_7::loadProduct',
                            widgetId   : widgetId,
                            categoryId : categoryId,
                        };

                        $.post( ajax , data, function() {}, 'json').done(function(response) {
                            loading.hide();
                            if(response.status === 'success') {
                                product_style_7_content.html(response.item);
                                product_style_7_link.attr('href', response.slug);
                                data_item[categoryId] = {
                                    item : response.item,
                                    url  : response.slug
                                };
                                product_style_7_slider(widgetId, config);
                            }
                        });
                    }
                }
                product_style_7_slider_load(<?php echo $main_category;?>, <?php echo $this->id;?>, pr_config_style_7);
                $(document).on('click', '#product_style_7_header_<?= $this->id;?> .product_style_7_category_list li.item a', function(){

                    categoryId = $(this).attr('data-id');

                    widgetId 	= $(this).attr('data-wg');

                    $('#product_style_7_header_' + widgetId +' .product_style_7_category_list li.item a').removeClass('active');

                    $(this).addClass('active');

                    product_style_7_slider_load(categoryId, widgetId, pr_config_style_7);

                    return false;
                });
                $('#product_style_7_header_<?= $this->id;?> .js_btn_show_tab').click(function() {
                    $(this).closest('.product_style_7_header_left').find('.product_style_7_category_list').toggle();
                    return false;
                });
            });
        </script>
        <?php
    }
    function css() { include_once('assets/product-style-7.css'); }
    static public function loadProduct($ci, $model) {

        $result['status']   = 'error';

        $result['message']  = 'Lấy dữ liệu thất bại';

        if(InputBuilder::post()) {

            $widgetID          = (int)InputBuilder::post('widgetId');

            $categoryID         = (int)InputBuilder::post('categoryId');

            $widget = Widget::get($widgetID);

            if(have_posts($widget)) {
                $slug    =  Url::permalink(URL_PRODUCT);
                $option = (object)unserialize($widget->options);
                $option->pr_display = widget_product_style_7::displayDefault($option->pr_display);
                $args = [
                    'where'  => ['public' => 1, 'trash' => 0],
                    'params' => ['orderby' => 'order, created desc', 'limit' => (!empty($option->limit)) ? $option->limit : 50]
                ];
                if(!empty($categoryID)) {
                    $args['where_category'] = ProductCategory::get($categoryID);
                    if(have_posts($args['where_category'])) $slug = Url::permalink($args['where_category']->slug);
                }
                if($option->pr_status >= 1 && $option->pr_status <= 3) {
                    $status = 'status'.$option->pr_status;
                    $args['where'][$status] = 1;
                }
                else if($option->pr_status == 4) $args['where']['price_sale <>'] = 0;
                $products = Product::gets($args);
                $result['item'] = '';
                $widget = new widget_product_style_7();
                $widget->id = $widgetID;
                ob_start();
                if($option->pr_display['type'] == 0) $widget->displayHorizontal($products, $option->pr_display['rows']);
                if($option->pr_display['type'] == 1) $widget->displayList($products, $option);
                $result['item'] = ob_get_contents();
                ob_clean();
                ob_end_flush();
                $result['status']   = 'success';
                $result['slug']     = $slug;
            }
        }
        echo json_encode( $result );
    }
    static public function displayDefault($display) {
        if(!is_array($display)) $display = [];
        if(!isset($display['type'])) $display['type'] = 0;
        if(!isset($display['margin'])) $display['margin'] = 15;
        if(!isset($display['time'])) $display['time'] = 3;
        if(!isset($display['speed'])) $display['speed'] = 0.7;
        if(!isset($display['rows'])) $display['rows'] = 1;
        return $display;
    }
}

Widget::add('widget_product_style_7');

Ajax::client('widget_product_style_7::loadProduct');

function _form_product_display_style_7($param, $value = []) {

    $value = widget_product_style_7::displayDefault($value);

    $output = '';

    $Form = new FormBuilder();

    ob_start();
    ?>
    <div class="row">
        <!-- TAB NAVIGATION -->
        <ul class="nav nav-tabs">
            <li class="active" data-tab="#pr_display_slider">
                <label for="pr_display_type_0">
                    <input class="pr_display_type" id="pr_display_type_0" type="radio" name="pr_display[type]" value="0" <?php echo ($value['type'] == 0) ? 'checked' : '';?>> Sản phẩm chạy ngang
                </label>
            </li>
            <li data-tab="#pr_display_list">
                <label for="pr_display_type_1">
                    <input class="pr_display_type" id="pr_display_type_1" type="radio" name="pr_display[type]" value="1" <?php echo ($value['type'] == 1) ? 'checked' : '';?>> Sản phẩm danh sách
                </label>
            </li>
        </ul>
        <!-- TAB CONTENT -->
        <div class="tab-content">
            <div class="<?php echo ($value['type'] == 0) ? 'active in' : '';?> tab-pane fade" id="pr_display_slider">
                <?php $Form->add('pr_display[margin]', 'number', ['label' => 'Khoảng cách giữa các sản phẩm', 'value' => 15], $value['margin']);?>
                <?php $Form->add('pr_display[time]', 'number', ['label' => 'Thời gian tự động chạy', 'value' => 3, 'step'=> '0.01'], $value['time']);?>
                <?php $Form->add('pr_display[speed]', 'number', ['label' => 'Thời gian hoàn thành chạy', 'value' => 0.7, 'step'=> '0.01'], $value['speed']);?>
                <?php $Form->add('pr_display[rows]', 'select', ['label' => 'Số hàng sản phẩm', 'options' => [1 => '1 hàng', 2 => '2 hàng']], $value['rows']);?>
                <?php $Form->html(false);?>
            </div>
            <div class="<?php echo ($value['type'] == 1) ? 'active in' : '';?> tab-pane fade" id="pr_display_list"></div>
        </div>
        <script defer>
            let tab = $('#widget_product_style_7 .tab-content .tab-pane');

            $('#widget_product_style_7 .nav-tabs li .pr_display_type').click(function () {
                let idTab = $(this).closest('li').attr('data-tab');
                tab.removeClass('active');
                tab.removeClass('in');
                $(idTab).addClass('active');
                $(idTab).addClass('in');
            });
        </script>
    </div>
    <?php
    $output = ob_get_contents();
    ob_end_clean();
    return $output;
}