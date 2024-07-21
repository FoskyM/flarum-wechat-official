<?php
use Flarum\Settings\SettingsRepositoryInterface;
use Flarum\User\Event\LoggedIn;
use FoskyM\WechatOfficial\Models\WechatLink;
use Psr\Http\Message\ServerRequestInterface;


class LoggedInListener
{
    protected $settings;
    protected $request;

    public function __construct(SettingsRepositoryInterface $settings, ServerRequestInterface $request)
    {
        $this->settings = $settings;
        $this->request = $request;
    }

    public function handle(LoggedIn $event)
    {
        $user = $event->user;

        $session = $this->request->getAttribute('session');
        if ($session->get('wechat_open_id')) {
            $wechatLink = new WechatLink;
            $wechatLink->user_id = $user->id;
            $wechatLink->wechat_open_id = $session->get('wechat_open_id');
            $wechatLink->wechat_original_data = $session->get('wechat_original_data');
            $wechatLink->save();
            $session->remove('wechat_open_id');
            $session->remove('wechat_original_data');
        }
    }
}