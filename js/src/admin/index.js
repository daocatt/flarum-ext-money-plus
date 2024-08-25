import {extend, override} from 'flarum/extend';

app.initializers.add('gtdxyz-money-plus', () => {
  app.extensionData
    .for('gtdxyz-money-plus')
    .registerSetting({
      setting: 'gtdxyz-money-plus.moneyname',
      label: app.translator.trans('gtdxyz-money-plus.admin.settings.moneyname'),
      type: 'text',
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-money-plus.admin.settings.moneyforregistration')}</label>
          <input type="number" className="FormControl" step="any" bidi={this.setting('gtdxyz-money-plus.moneyforregistration')} />
        </div>
      );
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-money-plus.admin.settings.moneyforpost')}</label>
          <input type="number" className="FormControl" step="any" bidi={this.setting('gtdxyz-money-plus.moneyforpost')} />
        </div>
      );
    })
    .registerSetting({
      setting: 'gtdxyz-money-plus.postminimumlength',
      label: app.translator.trans('gtdxyz-money-plus.admin.settings.postminimumlength'),
      type: 'number',
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-money-plus.admin.settings.moneyfordiscussion')}</label>
          <input type="number" className="FormControl" step="any" bidi={this.setting('gtdxyz-money-plus.moneyfordiscussion')} />
        </div>
      );
    })
    .registerSetting(function () {
      return (
        <div className="Form-group">
          <label>{app.translator.trans('gtdxyz-money-plus.admin.settings.moneyforlike')}</label>
          <div class="helpText">{app.translator.trans('gtdxyz-money-plus.admin.settings.helpextensionlikes')}</div>
          <input type="number" className="FormControl" step="any" bidi={this.setting('gtdxyz-money-plus.moneyforlike')} />
        </div>
      );
    })
    .registerSetting({
      setting: 'gtdxyz-money-plus.autoremove.hidden',
      label: app.translator.trans('gtdxyz-money-plus.admin.autoremove.1'),
      type: 'checkbox',
    })
    .registerSetting({
      setting: 'gtdxyz-money-plus.autoremove.deleted',
      label: app.translator.trans('gtdxyz-money-plus.admin.autoremove.2'),
      type: 'checkbox',
    })
    .registerSetting({
      setting: 'gtdxyz-money-plus.noshowzero',
      label: app.translator.trans('gtdxyz-money-plus.admin.settings.noshowzero'),
      type: 'checkbox',
    })
    .registerPermission(
      {
        icon: 'fas fa-money-bill',
        label: app.translator.trans('gtdxyz-money-plus.admin.permissions.edit_money_label'),
        permission: 'user.edit_money',
      },
      'moderate',
    );
});
