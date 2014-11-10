<?php include 'common/header.php';?>

    <div class="container">
        <div class="row">
            <div class="col-md-8">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">创建新主题</h3>
                    </div>
                    <div class="panel-body">
                        <?php echo validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                        <?php echo form_open('topic/add', array('role' => 'form', 'id'=>'topic-add'));?>
                            <div class="form-group">
                                <label for="title">标题</label>
                                <input type="text" class="form-control" id="title"  name="title" placeholder="标题" value="<?php echo set_value('title');?>">
                            </div>
                            <div class="form-group">
                                <label for="nid">节点</label>
                                <select class="form-control" id="nid"  name="nid">
                                    <option value ="">请选择分类</option>
                                    <?php
                                    foreach ($nodes[0] as $node) { //$nodes[0]表示无父类
                                      if (array_key_exists($node['nid'], $nodes)) { //检查无父类的分类是否有子类
                                        echo '<optgroup label="'.$node['nname'].'">';
                                        foreach ($nodes[$node['nid']] as $childnode) {
                                          echo '<option value ="'.$childnode['nid'].'">'.$childnode['nname'].'</option>';
                                        }
                                        echo '</optgroup>';
                                      }else{
                                        echo '<option value ="'.$node['nid'].'">'.$node['nname'].'</option>';
                                      }
                                    } ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <script src="<?php echo base_url('static/editor/kindeditor.js');?>"></script>
                                <script src="<?php echo base_url('static/editor/lang/zh_CN.js');?>"></script>
                                <script>
                                    KindEditor.ready(function(K) {
                                        var options = {
                                            autoHeightMode : true,
                                            afterCreate : function() {
                                                this.html('<p><br/></p>');
                                                this.focus();
                                                this.loadPlugin('autoheight');
                                            },
                                            items : [
                                            'source', 'preview', '|', 'fontsize', 'bold', 'italic', 'underline',
                                            'strikethrough', '|', 'justifyleft', 'justifycenter', 'justifyright', 'insertorderedlist',
                                            'insertunorderedlist', '|', 'emoticons', 'image', 'flash', 'link']
                                        };
                                        window.editor = K.create('#content', options);
                                    });
                                </script>
                                <textarea id="content" name="content" style="width:100%;height:300px;visibility:hidden;"><?php echo set_value('content');?></textarea>
                            </div>
                            <button type="submit" class="btn btn-default">提交</button>
                        </form>
                    </div>
                </div>
            </div><!-- /.col-md-8 -->

<?php include 'common/sidebar.php';?>

        </div><!-- /.row -->
    </div><!-- /.container -->

<?php include 'common/footer.php';?>
</body>
</html>