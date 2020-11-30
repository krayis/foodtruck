import React, {Component} from 'react';
import axios from 'axios'
import Skeleton from 'react-loading-skeleton';
import CartContext from '../CartContext';
import {v4 as uuidv4} from 'uuid';

const duration = 225;

const overlayDefaultStyle = {
    opacity: 0,
    transition: `opacity ${duration}ms linear 0s`,
};

const overlayTransitionStyles = {
    entering: {},
    entered: {
        opacity: 1,
    },
    exiting: {
        opacity: 0,
    },
    exited: {
        opacity: 0,
    },
};

const modalDefaultStyle = {
    opacity: 0,
    transition: `transform ${duration}ms ease-in-out 0s, opacity ${duration}ms linear 0s`,
    transform: `translate3d(0px, 0px, 0px) scale(.95)`
};

const modalTransitionStyles = {
    entering: {},
    entered: {
        transform: `translate3d(0px, 0px, 0px) scale(1)`,
        opacity: 1,
    },
    exiting: {
        transform: `translate3d(0px, 0px, 0px) scale(.8)`
    },
    exited: {
        transform: `translate3d(0px, 0px, 0px) scale(.8)`
    },
};

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
        this.closeModal = this.closeModal.bind(this);
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
        const id = target.getAttribute('data-id');
        const value = parseInt(target.value);
        let modifiers = this.state.modifiers;

        const modifier = {
            id,
            quantity: value,
            name,
            price,
            categoryId
        };

        console.log(modifier)
        if (target.type === 'radio') {
            modifiers = modifiers.filter(m => m.categoryId !== categoryId);
            modifiers.push(modifier)
        }

        if (target.type === 'checkbox') {
            if (target.checked) {
                modifiers.push(modifier);
            } else {
                modifiers = modifiers.filter(m => m.id !== id);
            }
        }

        if (target.type === 'select-one') {
            modifiers = modifiers.filter(m => m.id !== id);
            modifiers.push(modifier)
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

    buildOptions(modifier) {
        const arr = [];
        for (let i = modifier.min; i <= modifier.max; i++) {
            arr.push(<option key={i} value={i}>{i}</option>)
        }
        return arr;
    }

    closeModal(e) {
        if (e.target === e.currentTarget) {
            this.props.closeModal();
        }
    }
    render() {
        const item = this.state.item;
        const loading = this.state.loading;
        const state = this.props.state;
        return (
            <React.Fragment>
                <div className="item-modal-container" style={{
                    ...overlayDefaultStyle,
                    ...overlayTransitionStyles[state]
                }} onClick={(e) => this.closeModal(e)}>
                    <div className="item-modal" style={{
                        ...modalDefaultStyle,
                        ...modalTransitionStyles[state]
                    }}>
                        <form onSubmit={this.onSubmit}>
                            <div className="item-modal-content">
                                <div className="top-close-button" onClick={(e) => this.closeModal(e)} >
                                    <i className="icon ion-ios-close-circle"></i>
                                </div>
                                <h1>{loading ? <Skeleton width={170}/> : item.name}</h1>
                                {loading ? <Skeleton width={170}/> : (item.description &&
                                    <p>{item.description}</p>)}
                                {loading ? '' : item.thumbnail && <img src={`/storage/${item.thumbnail}`}/>}
                                {!loading && item.modifier_categories.length > 0 &&
                                item.modifier_categories.map((modifierCategory, key) =>
                                    <div className="item-meta" key={`item-modifier-category-${key}`}>
                                        <div className="item-modifier-heading">
                                            <div className="item-modifier-category">
                                                {modifierCategory.name}
                                            </div>
                                            {modifierCategory.type === 'EXACT' &&
                                            <div className="item-modifier-category-helper">
                                                <span>Please select {modifierCategory.max_permitted}</span>
                                            </div>
                                            }
                                            {modifierCategory.type === 'OPTIONAL_MAX' &&
                                            <div className="item-modifier-category-helper">
                                                <span>Choose up to {modifierCategory.max_permitted_per_option}</span>
                                            </div>
                                            }
                                            {modifierCategory.type === 'RANGE' &&
                                            <div className="item-modifier-category-helper">
                                                {modifierCategory.min_permitted === 0 && modifierCategory.max_permitted > 0 &&
                                                <span>Choose up to {modifierCategory.max_permitted}</span>}
                                                {modifierCategory.min_permitted === modifierCategory.max_permitted &&
                                                <span>Choose {modifierCategory.max_permitted}</span>}
                                                {modifierCategory.min_permitted !== modifierCategory.max_permitted && modifierCategory.min_permitted > 0 && modifierCategory.max_permitted > 0 &&
                                                <span>Choose a min of {modifierCategory.min_permitted} and max of {modifierCategory.max_permitted}</span>}
                                            </div>
                                            }
                                            <div
                                                className="item-modifier-type">{modifierCategory.type === 'OPTIONAL' ? 'Optional' : 'Required'}</div>
                                        </div>
                                        <ul>
                                            {modifierCategory.modifiers.map((modifier, key) =>
                                                <li key={`item-modifier-${key}`}>
                                                    {modifier.type === 'SINGLE' &&
                                                    <label>
                                                        <input
                                                            onChange={this.modifierUpdate}
                                                            name={`category-${modifierCategory.id}`}
                                                            value={1}
                                                            data-id={modifier.id}
                                                            data-name={modifier.name}
                                                            data-price={modifier.price}
                                                            data-category={modifier.modifier_group_id}
                                                            required={modifierCategory.modifier_category_type_id === 1 ? 'required' : ''}
                                                            type={['EXACT', 'OPTIONAL_MAX'].includes(modifierCategory.type) && modifierCategory.max_permitted == 1 ? 'radio' : 'checkbox'}/> {`${modifier.name}${modifier.price > 0 ? ` +$${modifier.price}` : ''}`}
                                                    </label>
                                                    }
                                                    {modifier.type === 'MULTIPLE' &&
                                                    <label>
                                                        {`${modifier.name}${modifier.price > 0 ? ` +$${modifier.price}` : ''}`}
                                                        <select
                                                            onChange={this.modifierUpdate}
                                                            name={`category-${modifierCategory.id}`}
                                                            value={1}
                                                            data-id={modifier.id}
                                                            data-name={modifier.name}
                                                            data-price={modifier.price}
                                                            data-category={modifier.modifier_group_id}
                                                        >
                                                            {this.buildOptions(modifier)}
                                                        </select>
                                                    </label>
                                                    }

                                                </li>
                                            )}
                                        </ul>
                                    </div>
                                )}

                                <div className="form-group">
                                    {loading ? <label htmlFor=""><Skeleton width="100%" width={110}/></label> :
                                        <label htmlFor="">Extra Instructions</label>}
                                    {loading ? <Skeleton width="100%" height={62}/> :
                                        <textarea></textarea>
                                    }
                                </div>
                            </div>
                            <div className="item-modal-footer">
                                <React.Fragment>
                                    {loading ? <Skeleton width={170} height={40} className="pull-right"/> :
                                        <button className="add" disabled={!this.isFormValid()}>
                                            Add to cart - ${this.totalPrice()}
                                        </button>
                                    }
                                    {loading ? <Skeleton width={60} height={40} className="pull-right"
                                                         style={{marginRight: '10px'}}/> :
                                        <input value={1} pattern="[0-9]*" />
                                    }
                                    {loading ? <Skeleton width={100} height={40}/> :
                                        <button className="close" onClick={this.closeModal}>Cancel</button>
                                    }

                                </React.Fragment>
                            </div>
                        </form>
                    </div>
                </div>
                )}
            </React.Fragment>
        )
    }
}

ItemModal.contextType = CartContext;

export default ItemModal;
