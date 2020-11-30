import React, {Component} from 'react';
import {Transition} from 'react-transition-group';
import CartContext from '../CartContext';
import axios from 'axios';
import {
    withRouter, Redirect
} from "react-router-dom";


const duration = 225;

const defaultStyle = {
    transition: `transform ${duration}ms ease-in-out 0s`,
    transform: `translate3d(100%, 0px, 0px) scale(1)`
};

const transitionStyles = {
    entering: {
        // transform: `translate3d(0%, 0px, 0px) scale(1)`
    },
    entered: {
        transform: `translate3d(0%, 0px, 0px) scale(1)`
    },
    exiting: {
        transform: `translate3d(100%, 0px, 0px) scale(1)`
    },
    exited: {
        transform: `translate3d(100%, 0px, 0px) scale(1)`
    },
};


class Cart extends Component {

    constructor(props) {
        super(props);
        this.checkout = this.checkout.bind(this);
        this.calculateGrandTotal = this.calculateGrandTotal.bind(this);
        this.calculateItemPrice = this.calculateItemPrice.bind(this);
    }

    calculateGrandTotal() {
        const cart = this.context.cart;
        let grandTotal = 0;
        for (let i = 0; i < cart.length; i++) {
            const item = cart[i];
            grandTotal += parseFloat(item.price);
            for (let j = 0; j < item.modifiers.length; j++) {
                const modifier = item.modifiers[j];
                grandTotal += parseFloat(modifier.price);
            }
        }
        return grandTotal.toFixed(2);
    }

    calculateItemPrice(item) {
        let price = parseFloat(item.price);
        for (let i = 0; i < item.modifiers.length; i++) {
            const modifier = item.modifiers[i];
            price += parseFloat(modifier.price);
        }
        return price.toFixed(2);
    }

    checkout() {
        const data = {
            cart: this.context.cart,
            truck: this.context.truck,
            event: this.context.event,
        };
        axios.post('/api/checkout', data).then((response) => {
            this.props.history.push(`/checkout/${response.data.uuid}`);
            this.context.closeCart();
        });
    }

    render() {
        const context = this.context;
        return (
            <Transition in={context.showCart} timeout={{
                appear: 0,
                enter: 225,
                exit: 225,
            }} mountOnEnter={true} unmountOnExit={true}>
                {state => (
                    <div className={`cart-container`} onClick={context.closeCart}>
                        <div className="cart" style={{
                            ...defaultStyle,
                            ...transitionStyles[state]
                        }} onClick={(e) => e.stopPropagation()}>
                            <div className="cart-content">
                                <div className="cart-header">
                                    <div className="close" onClick={context.closeCart}>
                                        <i className="icon ion-ios-close"></i>
                                    </div>
                                    <div className="cart-truck-label">Your Order</div>
                                    <div className="cart-truck-name">{context.truck.name}</div>
                                    <button onClick={this.checkout}>
                                        <span className="left">Checkout</span> <span
                                        className="right">${this.calculateGrandTotal()}</span>
                                    </button>
                                </div>
                                <ul className="cart-items">
                                    {this.context.cart.map((item, key) =>
                                        <li className="cart-item" key={`order-item-${key}`}>
                                            <div className="cart-item-quantity">
                                                1
                                            </div>
                                            <div className="cart-item-body">
                                                <div className="cart-item-details">
                                                    <div className="cart-item-name">
                                                        {item.name}
                                                        <div className="cart-item-price">
                                                            ${this.calculateItemPrice(item)}
                                                        </div>
                                                    </div>
                                                    <ul className="cart-item-modifiers">
                                                        {item.modifiers.map((modifier, key) =>
                                                            <li key={`order-item-modifiers-${key}`}>{modifier.name}</li>
                                                        )}
                                                    </ul>
                                                </div>
                                            </div>
                                            <div className="cart-item-delete"
                                                 onClick={() => context.removeItem(item._id)}>
                                                <i className="icon ion-ios-close"></i>
                                            </div>
                                        </li>
                                    )}
                                </ul>
                            </div>
                        </div>
                    </div>
                )}
            </Transition>


        )
    }
}

Cart.contextType = CartContext;

export default withRouter(Cart);
