import React, {Component} from 'react'
import axios from 'axios'
import {Link} from "react-router-dom"
import AnchorLink from 'react-anchor-link-smooth-scroll'
import {format, formatDistance, formatRelative, subDays} from 'date-fns'
import ItemModal from './ItemModal';

class OrderPage extends Component {

    constructor(props) {
        super(props);
        this.state = {
            showModal: false,
            selectedItemId: null,
            loading: true,
        };
        this.fetch();
        this.closeModal = this.closeModal.bind(this);
    }

    fetch() {
        axios.get(`/api/truck/${this.props.match.params.id}`).then(response => {
            this.setState({...response.data, loading: false});
        });
    }

    showModal(id) {
        this.setState({
            showModal: true,
            selectedItemId: id,
        });
    }

    closeModal() {
        this.setState({
            showModal: false,
        });
    }

    componentDidUpdate(prevProps) {
        if (this.props.location.search !== prevProps.location.search) {
            this.fetch();
        }
    }

    render() {
        if (this.state.loading === true) {
            return (
                'Loading...'
            )
        }
        const dateTimeParts = this.state.event.end_date_time.split(/[- :]/);
        dateTimeParts[1]--;
        const dateObject = new Date(...dateTimeParts);
        return (
            <React.Fragment>
                <div className="container--order">
                    <h1 className="food-truck-name">{this.state.name}</h1>
                    <div className="food-truck-address">Current location: {this.state.location.formatted_address}</div>
                    <div className="food-truck-schedule">Serving until {formatRelative(dateObject, new Date())}</div>
                </div>
                <div className="category-menu-wrapper">
                    <div className="container--order">
                        <ul className="category-menu">
                            {this.state.menu.map((category, key) =>
                                <li key={`category-nav-${category.id}`}>
                                    <AnchorLink offset="60" href={`#${category.name.toLowerCase()}`}>
                                        {category.name}
                                    </AnchorLink>
                                </li>
                            )}
                        </ul>
                    </div>
                </div>
                <div className="container--order">
                    <div className="menu">
                        {this.state.menu.map((category, key) =>
                            <div className="category" key={`category-${category.id}`}>
                                <div className="category-name" id={category.name.toLowerCase()}>{category.name}</div>
                                <ul className="items">
                                    {category.items.map((item, key) =>
                                        <li onClick={() => this.showModal(item.id)} className={`item ${item.thumbnail ? 'item--has-thumbnail' : ''}`}
                                            key={`item-${item.id}`}>
                                            <div className="item-inner">
                                                <div className="item-name">{item.name}</div>
                                                <div className="item-description">{item.description}</div>
                                                {item.thumbnail &&
                                                <div className="item-thumbnail"
                                                     style={{backgroundImage: `url('/storage/${item.thumbnail}')`}}></div>
                                                }
                                                <div className="item-price">${item.price}</div>
                                            </div>
                                        </li>
                                    )}
                                </ul>
                            </div>
                        )}
                    </div>
                </div>
                {this.state.showModal &&
                    <ItemModal
                        itemId={this.state.selectedItemId}
                        event={this.state.event}
                        closeModal={this.closeModal}/>
                }
            </React.Fragment>
        );
    }

}

export default OrderPage;
