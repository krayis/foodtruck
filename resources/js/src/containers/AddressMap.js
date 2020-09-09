import React from "react";
import {
    withScriptjs,
    GoogleMap,
    Marker, withGoogleMap
} from "react-google-maps";


const AddressMap = withScriptjs(withGoogleMap(props => {
    const defaultMapOptions = {
        disableDefaultUI: true,
        zoom: 16
    };
    return (
        <GoogleMap defaultZoom={defaultMapOptions.zoom} center={{lat: props.coordinates.latitude, lng: props.coordinates.longitude}} defaultCenter={{lat: props.coordinates.latitude, lng: props.coordinates.longitude}} defaultOptions={defaultMapOptions}>
            <Marker position={{lat: props.coordinates.latitude, lng:props.coordinates.longitude}}/>
        </GoogleMap>
    )
}));

export default AddressMap;
