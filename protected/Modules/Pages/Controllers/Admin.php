<?php

namespace App\Modules\Pages\Controllers;

use App\Components\Admin\Controller;
use App\Modules\Pages\Models\Page;

class Admin
    extends Controller
{

    /*
     protected function access($action)
     {
         return !empty($this->app->user) && $this->app->user->hasRole('admin');
     }
 */
    public function actionDefault()
    {
        $this->data->items = Page::findAllTree();
    }


    public function actionEdit($id=null, $parent=null)
    {
        $this->app->extensions->ckeditor->init();
        $this->app->extensions->ckfinder->init();

        if (null === $id || 'new' == $id) {
            $this->data->item = new Page();
            if (null !== $parent) {
                $this->data->item->parent = $parent;
            }
        } else {
            $this->data->item = Page::findByPK($id);
        }
    }

    public function actionSave($redirect = 0)
    {
        if (!empty($_POST[Page::PK])) {
            $item = Page::findByPK($_POST[Page::PK]);
        } else {
            $item = new Page();
        }
        $item
            ->fill($_POST)
            ->uploadFiles('files')
            ->save();
        if ($item->wasNew()) {
            $item->moveToFirstPosition();
        }
        if ($redirect) {
            $this->redirect('/pages/' . $item->url . '.html');
        } else {
            $this->redirect('/admin/pages/');        }

    }

    public function actionDelete($id)
    {
        $item = Page::findByPK($id);
        if ($item)
            $item->delete();
        $this->redirect('/admin/pages/');
    }

    public function actionDeleteFile($id)
    {
        $item = File::findByPK($id);
        if ($item) {
            $item->delete();
            $this->data->result = true;
        } else {
            $this->data->result = false;
        }
    }

    public function actionUp($id)
    {
        $item = Page::findByPK($id);
        if (empty($item))
            $this->redirect('/admin/pages/');
        $sibling = $item->getPrevSibling();
        if (!empty($sibling)) {
            $item->insertBefore($sibling);
        }
        $this->redirect('/admin/pages/');
    }

    public function actionDown($id)
    {
        $item = Page::findByPK($id);
        if (empty($item))
            $this->redirect('/admin/pages/');
        $sibling = $item->getNextSibling();
        if (!empty($sibling)) {
            $item->insertAfter($sibling);
        }
        $this->redirect('/admin/pages/');
    }

    public function actionMoveBefore($id, $to)
    {
        try {
            $item = Page::findByPK($id);
            if (empty($item)) {
                throw new Exception('Source element does not exist');
            }
            $destination = Page::findByPK($to);
            if (empty($destination)) {
                throw new Exception('Destination element does not exist');
            }
            $item->insertBefore($destination);
            $this->data->result = true;
        } catch (Exception $e) {
            $this->data->result = false;
            $this->data->error = $e->getMessage();
        }
    }

    public function actionMoveAfter($id, $to)
    {
        try {
            $item = Page::findByPK($id);
            if (empty($item)) {
                throw new Exception('Source element does not exist');
            }
            $destination = Page::findByPK($to);
            if (empty($destination)) {
                throw new Exception('Destination element does not exist');
            }
            $item->insertAfter($destination);
            $this->data->result = true;
        } catch (Exception $e) {
            $this->data->result = false;
            $this->data->error = $e->getMessage();
        }
    }

}