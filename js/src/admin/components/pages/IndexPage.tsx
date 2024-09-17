import app from 'flarum/admin/app';
import Page from 'flarum/common/components/Page';
import AdminPage from 'flarum/admin/components/AdminPage';
import LoadingIndicator from 'flarum/common/components/LoadingIndicator';
import Alert from 'flarum/common/components/Alert';
import Button from 'flarum/common/components/Button';
import Switch from 'flarum/common/components/Switch';
import FieldSet from 'flarum/common/components/FieldSet';
import type { SaveSubmitEvent } from 'flarum/admin/components/AdminPage';
import type { AlertIdentifier } from 'flarum/common/states/AlertManagerState';
import type Mithril from 'mithril';

export default class IndexPage extends AdminPage {
  user = app.session.user;
  oninit(vnode: Mithril.Vnode) {
    super.oninit(vnode);
  }

  refresh() {
    m.redraw();
  }

  header(vnode: Mithril.Vnode) {
    return <></>;
  }

  content() {
    const settings = app.extensionData.getSettings('foskym-wechat-official');

    return (
      <div className="ExtensionPage-settings">
        <div className="">
          <div className="Form">
            {settings!!.map(this.buildSettingComponent.bind(this))}

            {this.submitButton()}
          </div>
        </div>
      </div>
    );
  }

  saveSettings(e: SaveSubmitEvent) {
    return super.saveSettings(e).then(() => this.refresh());
  }
}
