import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import NotificationGrid from 'flarum/forum/components/NotificationGrid';
import SettingsPage from 'flarum/forum/components/SettingsPage';
import Alert from 'flarum/common/components/Alert';
import Button from 'flarum/common/components/Button';
import LinkButton from 'flarum/common/components/LinkButton';
import Link from 'flarum/common/components/Link';
import Page from 'flarum/common/components/Page';
import icon from 'flarum/common/helpers/icon';
import User from 'flarum/common/models/User';
import Model from 'flarum/common/Model';

app.initializers.add('foskym/flarum-wechat-official', () => {
  User.prototype.WechatAuth = Model.attribute('WechatAuth');

  extend(NotificationGrid.prototype, 'notificationMethods', function (items) {
    if (!app.forum.attribute('foskym-wechat-official.enable_push')) {
      return;
    }

    items.add('push', {
      name: 'push',
      icon: 'fab fa-weixin',
      label: app.translator.trans('foskym-wechat-official.forum.settings.push_notification_label'),
    });
  });

  extend(SettingsPage.prototype, 'accountItems', function (items) {
    const appid = app.forum.attribute('foskym-wechat-official.app_id');
    const callback_url = app.forum.attribute('baseUrl') + '/wechat-official/callback';
    const scope = 'snsapi_userinfo';
    const state = 'wechat';
    // https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx807d86fb6b3d4fd2&redirect_uri=http%3A%2F%2Fdevelopers.weixin.qq.com&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect
    let url = `https://open.weixin.qq.com/connect/oauth2/authorize?appid=${appid}&redirect_uri=${encodeURIComponent(
      callback_url
    )}&response_type=code&scope=${scope}&state=${state}#wechat_redirect`;
    items.add(
      'wechat',
      this.user.WechatAuth().isLinked ? (
        <Button
          icon="fab fa-weixin"
          className="Button Button--Wechat"
          onclick={() => {
            app
              .request({
                method: 'POST',
                url: app.forum.attribute('apiUrl') + '/wechat-official/unlink',
              })
              .then(() => {
                this.user.WechatAuth().isLinked = false;
                app.alerts.show(Alert, {
                  type: 'success',
                  children: app.translator.trans('foskym-wechat-official.forum.settings.wechat_unlink_success'),
                });
                m.redraw();
              });
          }}
        >
          {app.translator.trans('foskym-wechat-official.forum.settings.wechat_unlink')}
        </Button>
      ) : (
        <LinkButton
          icon="fab fa-weixin"
          className="Button Button--Wechat"
          // disabled={}
          href={url}
          external={true}
        >
          {this.user.WechatAuth().isLinked
            ? app.translator.trans('foskym-wechat-official.forum.settings.wechat_linked')
            : app.translator.trans('foskym-wechat-official.forum.settings.bind_wechat')}
        </LinkButton>
      ),
      -100
    );
  });
});
