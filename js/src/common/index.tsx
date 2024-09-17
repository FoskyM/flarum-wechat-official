import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend';
import EditUserModal from 'flarum/common/components/EditUserModal';

export { default as extend } from './extend';
import User from 'flarum/common/models/User';
import Model from 'flarum/common/Model';
// @ts-ignore
User.prototype.WechatAuth = Model.attribute('WechatAuth');
extend(EditUserModal.prototype, 'fields', function (items) {
  const user = this.attrs.user;

  if (this.attrs.user.canEditCredentials() && this.attrs.user.WechatAuth().wechat_open_id) {
    items.add(
      'wechat-official-openid',
      <div className="Form-group">
        <label>公众号 Openid</label>
        <input
          className="FormControl"
          placeholder={'公众号 Openid'}
          readonly
          value={this.attrs.user.WechatAuth().wechat_open_id}
        />
      </div>
    );
  }
});