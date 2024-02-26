import Modal from 'flarum/components/Modal';
import Button from 'flarum/components/Button';
import Stream from 'flarum/utils/Stream';

export default class UserMoneyModal extends Modal {
  oninit(vnode) {
    super.oninit(vnode);

    this.money = Stream(this.attrs.user.data.attributes['money'] || 0.0);
  }

  className() {
    return 'UserMoneyModal Modal--small';
  }

  title() {
    return app.translator.trans('gtdxyz-money-plus.forum.modal.title', {user: this.attrs.user});
  }

  content() {
    const moneyName = app.forum.attribute('gtdxyz-money-plus.moneyname') || 'MO';

    return (
      <div className="Modal-body">
        <div className="Form">
          <div className="Form-group">
            <label>{app.translator.trans('gtdxyz-money-plus.forum.modal.current')} {this.money()}{moneyName}</label>
            <input required className="FormControl" type="number" step="any" bidi={this.money} />
          </div>
          <div className="Form-group">
            {Button.component(
              {
                className: 'Button Button--primary',
                type: 'submit',
                loading: this.loading,
              },
              app.translator.trans('gtdxyz-money-plus.forum.modal.submit_button')
            )}
          </div>
        </div>
      </div>
    );
  }

  onsubmit(e) {
    e.preventDefault();

    this.loading = true;

    this.attrs.user
    .save({money: this.money()}, { errorHandler: this.onerror.bind(this) })
    .then(this.hide.bind(this))
    .catch(() => {
      this.loading = false;
      m.redraw();
    });
  }
}
