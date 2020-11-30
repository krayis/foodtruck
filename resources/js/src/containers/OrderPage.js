import React, {Component} from 'react'
import axios from 'axios'
import {useLocation, Link} from 'react-router-dom';
import Skeleton from 'react-loading-skeleton';
import AnchorLink from 'react-anchor-link-smooth-scroll'
import urlSlug from 'url-slug';
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
        axios.get(`/api/store/${this.props.match.params.id}`).then(response => {
            this.setState({...response.data, loading: false});
            const parts = this.props.match.params.id.split('-');
            const id = parts[parts.length - 1];
            const link = `/store/${urlSlug(response.data.name)}-${id}`;
            this.props.history.push(link)
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
        const loading = this.state.loading;
        let dateTimeParts, dateObject;
        if (!loading) {
            dateTimeParts = this.state.event.end_date_time.split(/[- :]/);
            dateTimeParts[1]--;
            dateObject = new Date(...dateTimeParts);
        }
        return (
            <React.Fragment>
                <div className="container--order">
                    <h1 className="food-truck-name">{loading ? <Skeleton width={170} /> : this.state.name}</h1>
                    <div className="food-truck-address">{loading ? <Skeleton width={300} />  : `Current location: ${this.state.location.formatted_address}`}</div>
                    <div className="food-truck-schedule">{loading ? <Skeleton width={190} /> : `Serving until ${formatRelative(dateObject, new Date())}`}</div>
                </div>
                <div className="category-menu-wrapper">
                    <div className="container--order">
                        <ul className="category-menu">
                            { loading &&
                            <React.Fragment>
                                <li><a><Skeleton width={60} /></a></li>
                                <li><a><Skeleton width={60} /></a></li>
                                <li><a><Skeleton width={60} /></a></li>
                                <li><a><Skeleton width={60} /></a></li>
                                <li><a><Skeleton width={60} /></a></li>
                            </React.Fragment>
                            }
                            {!loading && this.state.menu.map((category, key) =>
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
                        {loading &&
                        <div className="category">
                            <div className="category-name"><Skeleton width={140} /></div>
                            <ul className="items">
                                <li className="item"><Skeleton height={120} /></li>
                                <li className="item"><Skeleton height={120} /></li>
                                <li className="item"><Skeleton height={120} /></li>
                                <li className="item"><Skeleton height={120} /></li>
                            </ul>
                            <div className="category-name"><Skeleton width={140} /></div>
                            <ul className="items">
                                <li className="item"><Skeleton height={120} /></li>
                                <li className="item"><Skeleton height={120} /></li>
                                <li className="item"><Skeleton height={120} /></li>
                                <li className="item"><Skeleton height={120} /></li>
                            </ul>
                        </div>
                        }
                        {!loading && this.state.menu.map((category, key) =>
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
