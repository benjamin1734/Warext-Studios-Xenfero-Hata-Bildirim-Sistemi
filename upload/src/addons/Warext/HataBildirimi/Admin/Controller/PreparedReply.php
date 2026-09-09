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
        $replies = $this->finder('Warext\\HataBildirimi:PreparedReply')
            ->order('display_order')
            ->order('title')
            ->fetch();

        return $this->view('Warext\\HataBildirimi:PreparedReplyList', 'wrxt_hata_prepared_reply_list', [
            'replies' => $replies
        ]);
    }

    public function actionAdd()
    {
        $reply = $this->em()->create('Warext\\HataBildirimi:PreparedReply');
        $reply->active = true;
        $reply->display_order = 10;

        return $this->replyEdit($reply);
    }

    public function actionEdit()
    {
        $reply = $this->assertPreparedReplyExists();
        return $this->replyEdit($reply);
    }

    protected function replyEdit(\Warext\HataBildirimi\Entity\PreparedReply $reply)
    {
        return $this->view('Warext\\HataBildirimi:PreparedReplyEdit', 'wrxt_hata_prepared_reply_edit', [
            'reply' => $reply
        ]);
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

        return $this->redirect($this->buildLink('wrxt-hata-hazir-cevaplar'));
    }

    public function actionDelete()
    {
        $reply = $this->assertPreparedReplyExists();

        if ($this->isPost())
        {
            $reply->delete();
            return $this->redirect($this->buildLink('wrxt-hata-hazir-cevaplar'));
        }

        return $this->view('Warext\\HataBildirimi:PreparedReplyDelete', 'public:delete_confirm', [
            'title' => $reply->title,
            'formAction' => $this->buildLink('wrxt-hata-hazir-cevaplar/delete', null, ['prepared_reply_id' => $reply->prepared_reply_id])
        ]);
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
