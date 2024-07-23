import app from 'flarum/forum/app';
import Notification from 'flarum/forum/components/Notification';
import username from 'flarum/common/helpers/username';

export default class WechatLinkedNotification extends Notification {
  icon() {
    return 'fab fa-weixin';
  }

  href() {
    return '/settings';
  }

  content() {
    const notification = this.attrs.notification;
    const content = notification.content() || {};
    if (content.is_unlinked) {
      return app.translator.trans('foskym-wechat-official.forum.notifications.wechat_unlinked');
    }
    return app.translator.trans('foskym-wechat-official.forum.notifications.wechat_linked');
  }
}