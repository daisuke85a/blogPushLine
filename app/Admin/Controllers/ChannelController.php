<?php

namespace App\Admin\Controllers;

use App\Channel;
use Encore\Admin\Controllers\AdminController;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Show;

class ChannelController extends AdminController
{
    /**
     * Title for current resource.
     *
     * @var string
     */
    protected $title = 'App\Channel';

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        $grid = new Grid(new Channel);

        $grid->column('id', __('Id'));
        // $grid->column('access_token', __('Access token'));
        // $grid->column('channel_secret', __('Channel secret'));
        $grid->column('keyword', __('Keyword'));
        $grid->column('created_at', __('Created at'));
        $grid->column('updated_at', __('Updated at'));

        return $grid;
    }

    /**
     * Make a show builder.
     *
     * @param mixed $id
     * @return Show
     */
    protected function detail($id)
    {
        $show = new Show(Channel::findOrFail($id));

        $show->field('id', __('Id'));
        $show->field('keyword', __('Keyword'));
        $show->field('access_token', __('Access token'));
        $show->field('channel_secret', __('Channel secret'));
        $show->field('created_at', __('Created at'));
        $show->field('updated_at', __('Updated at'));

        return $show;
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        $form = new Form(new Channel);

        $form->text('keyword', __('Keyword'));
        $form->text('access_token', __('Access token'));
        $form->text('channel_secret', __('Channel secret'));

        return $form;
    }
}
