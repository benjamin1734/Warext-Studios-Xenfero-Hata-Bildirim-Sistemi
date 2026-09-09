<?php

namespace Warext\HataBildirimi\Admin\Controller;

use XF\Admin\Controller\AbstractController;
use XF\Mvc\ParameterBag;

class PreparedReply extends AbstractController
{
    protected function preDispatchController($action, ParameterBag $params)
    {
        $this->assertAdminPermission('wrxtHataManage');
    }

    public function actionIndex()
    {
        return $this->redirect($this->managerLink());
    }

    public function actionAdd()
    {
        return $this->redirect($this->managerLink(['prepared_reply_create' => 1]));
    }

    public function actionEdit()
    {
        $reply = $this->assertPreparedReplyExists();

        return $this->redirect($this->managerLink([
            'prepared_reply_edit_id' => $reply->prepared_reply_id
        ]));
    }

    public function actionSave()
    {
        $this->assertPostOnly();

        $replyId = $this->filter('prepared_reply_id', 'uint');
        $reply = $replyId
            ? $this->em()->find('Warext\\HataBildirimi:PreparedReply', $replyId)
            : $this->em()->create('Warext\\HataBildirimi:PreparedReply');

        if (!$reply)
        {
            return $this->notFound('Hazır cevap bulunamadı.');
        }

        $title = trim($this->filter('title', 'str'));
        $message = trim($this->plugin('XF:Editor')->fromInput('message'));
        $categoryId = $this->filter('prepared_reply_category_id', 'uint');

        if ($title === '')
        {
            return $this->error('Hazır cevap başlığı boş bırakılamaz.');
        }
        if ($message === '')
        {
            return $this->error('Hazır cevap içeriği boş bırakılamaz.');
        }
        if ($categoryId && !$this->em()->find('Warext\\HataBildirimi:PreparedReplyCategory', $categoryId))
        {
            return $this->error('Seçilen hazır cevap kategorisi bulunamadı.');
        }

        $reply->prepared_reply_category_id = $categoryId;
        $reply->title = mb_substr($title, 0, 100);
        $reply->message = $message;
        $reply->display_order = $this->filter('display_order', 'uint');
        $reply->active = $this->filter('active', 'bool');
        if (!$reply->exists())
        {
            $reply->created_date = \XF::$time;
        }
        $reply->updated_date = \XF::$time;
        $reply->save();

        return $this->redirect($this->managerLink(), 'Hazır cevap kaydedildi.');
    }

    public function actionDelete()
    {
        $this->assertPostOnly();
        $reply = $this->assertPreparedReplyExists();
        $reply->delete();

        return $this->redirect($this->managerLink(), 'Hazır cevap silindi.');
    }

    public function actionCategoryAdd()
    {
        return $this->redirect($this->managerLink(['prepared_category_create' => 1]));
    }

    public function actionCategoryEdit()
    {
        $category = $this->assertPreparedReplyCategoryExists();

        return $this->redirect($this->managerLink([
            'prepared_category_edit_id' => $category->prepared_reply_category_id
        ]));
    }

    public function actionCategorySave()
    {
        $this->assertPostOnly();

        $categoryId = $this->filter('prepared_reply_category_id', 'uint');
        $category = $categoryId
            ? $this->em()->find('Warext\\HataBildirimi:PreparedReplyCategory', $categoryId)
            : $this->em()->create('Warext\\HataBildirimi:PreparedReplyCategory');

        if (!$category)
        {
            return $this->notFound('Hazır cevap kategorisi bulunamadı.');
        }

        $title = trim($this->filter('category_title', 'str'));
        if ($title === '')
        {
            return $this->error('Kategori adı boş bırakılamaz.');
        }

        $category->title = mb_substr($title, 0, 100);
        $category->display_order = $this->filter('category_display_order', 'uint');
        if (!$category->exists())
        {
            $category->created_date = \XF::$time;
        }
        $category->updated_date = \XF::$time;
        $category->save();

        return $this->redirect($this->managerLink(), 'Hazır cevap kategorisi kaydedildi.');
    }

    public function actionCategoryDelete()
    {
        $this->assertPostOnly();
        $category = $this->assertPreparedReplyCategoryExists();

        $this->app->db()->update(
            'xf_wrxt_bug_prepared_reply',
            ['prepared_reply_category_id' => 0],
            'prepared_reply_category_id = ?',
            $category->prepared_reply_category_id
        );
        $category->delete();

        return $this->redirect($this->managerLink(), 'Kategori silindi. İçindeki hazır cevaplar Kategorisiz bölümüne taşındı.');
    }

    protected function managerLink(array $params = []): string
    {
        return $this->buildLink('wrxt-hata-bildirimleri', null, array_merge(['prepared_replies' => 1], $params));
    }

    protected function assertPreparedReplyExists(): \Warext\HataBildirimi\Entity\PreparedReply
    {
        $replyId = $this->filter('prepared_reply_id', 'uint');
        $reply = $this->em()->find('Warext\\HataBildirimi:PreparedReply', $replyId);
        if (!$reply)
        {
            throw $this->exception($this->notFound('Hazır cevap bulunamadı.'));
        }

        return $reply;
    }

    protected function assertPreparedReplyCategoryExists(): \Warext\HataBildirimi\Entity\PreparedReplyCategory
    {
        $categoryId = $this->filter('prepared_reply_category_id', 'uint');
        $category = $this->em()->find('Warext\\HataBildirimi:PreparedReplyCategory', $categoryId);
        if (!$category)
        {
            throw $this->exception($this->notFound('Hazır cevap kategorisi bulunamadı.'));
        }

        return $category;
    }
}
