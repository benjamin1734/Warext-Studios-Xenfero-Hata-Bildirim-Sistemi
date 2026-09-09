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
        return $this->redirect($this->buildLink('wrxt-hata-bildirimleri', null, ['prepared_replies' => 1]));
    }

    public function actionAdd()
    {
        return $this->redirect($this->buildLink('wrxt-hata-bildirimleri', null, ['prepared_replies' => 1, 'prepared_reply_edit_id' => 0]));
    }

    public function actionEdit()
    {
        $reply = $this->assertPreparedReplyExists();
        return $this->redirect($this->buildLink('wrxt-hata-bildirimleri', null, [
            'prepared_replies' => 1,
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
        $message = trim($this->filter('message', 'str'));
        if ($title === '')
        {
            return $this->error('Hazır cevap başlığı boş bırakılamaz.');
        }
        if ($message === '')
        {
            return $this->error('Hazır cevap içeriği boş bırakılamaz.');
        }

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

        return $this->redirect($this->buildLink('wrxt-hata-bildirimleri', null, ['prepared_replies' => 1]));
    }

    public function actionDelete()
    {
        $this->assertPostOnly();
        $reply = $this->assertPreparedReplyExists();
        $reply->delete();

        return $this->redirect($this->buildLink('wrxt-hata-bildirimleri', null, ['prepared_replies' => 1]));
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
}
