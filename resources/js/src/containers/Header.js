import React, {Component} from 'react';
import menuIcon from '../images/menu.svg';
import AddressInput from './AddressInput';
import CartContext from '../CartContext';
import {
    Link,
    withRouter
} from 'react-router-dom';

class Header extends Component {

    constructor(props) {
        super(props);
        this.isCheckout = this.isCheckout.bind(this);
        this.isCheckout()
    }

    isCheckout() {
        return !this.props.location.pathname.includes('/checkout/');
    }

    render() {
        return (
            <div className="navbar">
                {this.isCheckout() &&
                    <div className="container">
                        <div className="left">
                            <div className="nav">
                                <img src={menuIcon} />
                            </div>
                            <AddressInput/>
                        </div>
                        <ul className="right">
                            <li className="search-wrapper">
                                <div className="search-wrapper-inner">
                                    <span className="search-icon">
                                        <i className="icon ion-ios-search"></i>
                                    </span>
                                    <input
                                        type="text"
                                        placeholder="Food truck name or ID"
                                        autoCapitalize="off"
                                        autoCorrect="off"
                                        aria-expanded="false"
                                        aria-autocomplete="both"
                                    />
                                </div>
                            </li>
                            <CartContext.Consumer>
                                {(context) => (
                                    <li>
                                        <div className="cart-icon" onClick={context.openCart}>
                                            <i className="icon ion-ios-cart"></i> {context.cart.length}
                                        </div>
                                    </li>
                                )}
                            </CartContext.Consumer>
                        </ul>
                    </div>
                }
                {!this.isCheckout() &&
                    <div className="container">
                        <ul className="left">
                            <CartContext.Consumer>
                                {(context) => (
                                    <li className="back-button">
                                        <Link to={`/menu/${context.truck.id}`}><i className="icon ion-ios-arrow-round-back"></i> Back to menu</Link>
                                    </li>
                                )}
                            </CartContext.Consumer>
                        </ul>
                    </div>
                }
            </div>
        );
    }

}

export default withRouter(Header);
