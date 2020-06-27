<style>
    a.widget-edit-link {
        top: -25px;
        right: -10px;
        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 0;
        font-size: 12px;
        font-weight: 400;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        color: #FFFFFF;
        background-color: rgba(48, 48, 48, 0.3);
        border: 1px solid rgba(48, 48, 48, 0.7);
        border-radius: 4px;
        z-index: 99;
    }

    a.widget-edit-link:hover,
    a.widget-edit-link:focus,
    a.widget-edit-link:active {
        color: #FFFFFF;
        background-color: rgba(48, 48, 48, 0.7);
        border-color: #333333;
        text-decoration: none;
        opacity: 1;
    }

    a.widget-edit-link .fa {
        position: relative;
        top: 2px;
        font-size: 20px;
    }
</style>
<?php echo $this->BcBaser->getLink('ウィジェットエリア「' . h($name) . '」編集', $edit_link, array('class' => 'widget-edit-link', 'target' => '_blank')); ?>