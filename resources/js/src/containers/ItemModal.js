import React, {Component} from 'react';
import axios from 'axios'
import CartContext from '../CartContext';
import { v4 as uuidv4 } from 'uuid';

function isModifierValid(itemModifiers, userModifiers) {
    let modifierIds = userModifiers.map(m => m.id);
    let validModifierIds = itemModifiers.modifiers.map(m => m.id);
    for (let i = 0; i < modifierIds.length; i++) {
        if (!validModifierIds.includes(modifierIds[i])) {
            return false;
        }
    }
    return true;
}

function verifyModifier(itemModifiers, userModifiers) {
    let valid = true;

    for (let i = 0; i < itemModifiers.length; i++) {
        const category = itemModifiers[i];
        const selectModifiers = userModifiers.filter(modifier => modifier.categoryId == category.id);

        if (category.modifier_category_type_id === 1) {
            if (selectModifiers.length === 0) {
                valid = false;
                continue;
            }
        }

        if (category.modifier_category_type_id === 2) {
            if (category.min === 0 && category.max === 0) {
                continue;
            }
            if (category.min > 0 && category.min > selectModifiers.length) {
                valid = false;
                continue;
            }
            if (category.max === 0) {
                continue;
            }
            if (category.max > 0 && category.max < selectModifiers.length) {
                valid = false;
                continue;
            }
            if (!isModifierValid(category, selectModifiers)) {
                valid = false
            }
        }
    }

    return valid;
}

class ItemModal extends Component {
    constructor(props) {
        super(props);
        this.state = {
            loading: true,
            modifiers: [],
            event: props.event,
        };
        this.fetch();
        this.totalPrice = this.totalPrice.bind(this);
        this.isFormValid = this.isFormValid.bind(this);
        this.modifierUpdate = this.modifierUpdate.bind(this);
        this.onSubmit = this.onSubmit.bind(this);
    }

    fetch() {
        axios.get(`/api/item/${this.props.itemId}`).then(response => {
            const item = {
                _id: uuidv4(),
                id: response.data.id,
                name: response.data.name,
                description: response.data.description,
                price: response.data.price,
                modifier_categories: response.data.modifier_categories,
            };
            this.setState({
                item: item,
                truck: response.data.truck,
                loading: false,
            });
        });
    }

    modifierUpdate(e) {
        const target = e.target;
        const categoryId = target.getAttribute('data-category');
        const name = target.getAttribute('data-name');
        const price = target.getAttribute('data-price');
        const value = parseInt(target.value);
        let modifiers = this.state.modifiers;

        const modifier = {
            id: value,
            name: name,
            price: price,
            categoryId: categoryId,
        };

        if (target.type === 'radio') {
            modifiers = modifiers.filter(m => m.categoryId !== categoryId);
            modifiers.push(modifier)
        }

        if (target.type === 'checkbox') {
            if (target.checked) {
                modifiers.push(modifier);
            } else {
                modifiers = modifiers.filter(m => m.id !== value);
            }
        }

        this.setState({modifiers});
    }

    onSubmit(e) {
        e.preventDefault();
        const line = {
            item: {
                _id: uuidv4,
                ...this.state.item,
                modifiers: this.state.modifiers
            },
            truck: this.state.truck,
            event: this.state.event,
        };
        this.context.addToCart(line);
        this.props.closeModal();
    }

    isFormValid() {
        if (this.state.item.modifier_categories.length === 0) {
            return true;
        }
        if (verifyModifier(this.state.item.modifier_categories, this.state.modifiers)) {
            return true;
        }
        return '';
    }

    totalPrice() {
        let price = parseFloat(this.state.item.price);
        const selectedModifiers = this.state.modifiers;
        for (let i = 0; i < selectedModifiers.length; i++) {
            const modifier = selectedModifiers[i];
            price += parseFloat(modifier.price);
        }
        return price;
    }

    render() {
        if (this.state.loading) {
            return (
                <div className="item-modal-container">
                    <div className="item-modal">
                        <div className="item-modal-content">
                            Loading...
                        </div>
                    </div>
                </div>
            )
        }
        const context = this.state.context;
        const item = this.state.item;
        return (

            <div className="item-modal-container">
                <div className="item-modal">
                    <form onSubmit={this.onSubmit}>
                        <div className="item-modal-content">
                            <div onClick={this.props.closeModal} className="top-close-button"><i
                                className="icon ion-ios-close-circle"></i></div>
                            <h1>{item.name}</h1>
                            {item.description && <p>{item.description}</p>}
                            {item.thumbnail && <img src={`/storage/${item.thumbnail}`}/>}
                            {item.modifier_categories.length > 0 &&
                            item.modifier_categories.map((modifierCategory, key) =>
                                <div className="item-meta" key={`item-modifier-category-${key}`}>
                                    <div className="item-modifier-heading">
                                        <div className="item-modifier-category">
                                            {modifierCategory.name}
                                        </div>
                                        {modifierCategory.modifier_category_type_id === 2 &&
                                        <div className="item-modifier-category-helper">
                                            {modifierCategory.min === 0 && modifierCategory.max === 0 &&
                                            <span> Select as many as you like</span>}
                                            {modifierCategory.min === 0 && modifierCategory.max > 0 &&
                                            <span> Choose up to {modifierCategory.max}</span>}
                                            {modifierCategory.min > 0 && modifierCategory.max === 0 &&
                                            <span> Choose a min of {modifierCategory.min}</span>}
                                            {modifierCategory.min > 0 && modifierCategory.max > 0 &&
                                            <span> Choose a min of {modifierCategory.min} and max of {modifierCategory.max}</span>}
                                        </div>
                                        }
                                        <div
                                            className="item-modifier-type">{modifierCategory.modifier_category_type_id === 1 ? 'Required' : 'Optional'}</div>
                                    </div>
                                    <ul>
                                        {modifierCategory.modifiers.map((modifier, key) =>
                                            <li key={`item-modifier-${key}`}>
                                                <label>
                                                    <input
                                                        onChange={this.modifierUpdate}
                                                        name={`category-${modifierCategory.id}`}
                                                        value={modifier.id}
                                                        data-name={modifier.name}
                                                        data-price={modifier.price}
                                                        data-category={modifierCategory.id}
                                                        required={modifierCategory.modifier_category_type_id === 1 ? 'required' : ''}
                                                        type={modifierCategory.modifier_category_type_id === 1 ? 'radio' : 'checkbox'}/> {`${modifier.name}${modifier.price > 0 ? ` + $${modifier.price}` : ''}`}
                                                </label>
                                            </li>
                                        )}
                                    </ul>
                                </div>
                            )}

                            <div className="form-group">
                                <label htmlFor="">Extra Instructions</label>
                                <textarea></textarea>
                            </div>
                        </div>
                        <div className="item-modal-footer">
                            <button className="add" disabled={!this.isFormValid()}>Add
                                to
                                cart
                                - ${this.totalPrice()}</button>
                            <input/>
                            <button className="close" onClick={this.props.closeModal}>Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        )
    }
}

ItemModal.contextType = CartContext;

export default ItemModal;
