import React, {Component} from 'react';
import {
    Link
} from "react-router-dom";
import Skeleton from 'react-loading-skeleton';
import axios from 'axios';
import queryString from 'query-string';


class SearchPage extends Component {
    constructor(props) {
        super(props);
        this.state = {
            trucks: [],
            loading: true,
            error: false,
        };
        this.fetchData();
    }

    fetchData() {
        const params = queryString.parse(location.search);
        axios.get(`/api/search/trucks`, {params}).then(response => {
            this.setState({
                trucks: response.data,
                loading: false,
                error: false,
            })
        }).catch(err => {
            this.setState({
                error: true,
            });
        });
    }

    componentDidUpdate(prevProps) {
        if (this.props.location.search !== prevProps.location.search) {
            this.setState({
                loading: true,
            });
            this.fetchData();
        }
    }

    render() {
        return (
            <div className="container--index">
                <div className="header">
                    <div className="topbar">

                        <h1>{this.state.loading ? <Skeleton
                            width={170}/> : `${this.state.trucks.length} ${this.state.trucks.length === 1 ? 'truck' : 'trucks'} found`}</h1>
                        <form className="form-inline">

                            {this.state.loading ? <Skeleton width={165} height={28}/> :
                                <React.Fragment>
                                    <label>Distance:</label>
                                    <select>
                                        <option value="5">5 miles</option>
                                        <option value="10">10 miles</option>
                                        <option value="25">25 miles</option>
                                        <option value="50">50 miles</option>
                                        <option value="100">100 miles</option>
                                        <option value="200">200 miles</option>
                                    </select>
                                </React.Fragment>
                            }
                        </form>
                    </div>
                </div>
                <ul className="truck-list">
                    {this.state.loading &&
                    <React.Fragment>
                        <li><Skeleton height={80}/></li>
                        <li><Skeleton height={80}/></li>
                        <li><Skeleton height={80}/></li>
                        <li><Skeleton height={80}/></li>
                    </React.Fragment>
                    }
                    {this.state.error === false && this.state.loading === false && this.state.trucks.map((item, key) =>
                        <li key={key}>
                            <Link to={`/store/${item.truck_id}`}>
                                {item.truck.thumbnails &&
                                <div className="featured-image">
                                    <div className="image-wrapper">
                                        <div className="image"
                                             style={{backgroundImage: "url('https://media-cdn.grubhub.com/image/upload/d_search:browse-images:default.jpg/w_460,h_127,q_auto,dpr_auto,c_fill,f_auto/rcorhmol9qmor2ceyxuu')"}}></div>
                                    </div>
                                    <div className="image-wrapper">
                                        <div className="image"
                                             style={{backgroundImage: "url('https://media-cdn.grubhub.com/image/upload/d_search:browse-images:default.jpg/w_460,h_127,q_auto,dpr_auto,c_fill,f_auto/rcorhmol9qmor2ceyxuu')"}}></div>
                                    </div>
                                </div>
                                }
                                <div className="truck-name">{item.truck.name}</div>
                                <div className="meta">
                                    <span className="left">Serving until 5:30am</span>
                                    <span
                                        className="right">{item.distance} {item.distance === 1 ? `mile` : `miles`} away</span>
                                </div>
                            </Link>
                        </li>
                    )}
                </ul>
            </div>
        );
    }
}

export default SearchPage;
