import React from 'react';
import ReactDOM from 'react-dom';



function Example() {
    return (
        <div className="container">
            I am war
        </div>
    );
}

export default Example;

if (document.getElementById('wrapper')) {
    ReactDOM.render(<Example />, document.getElementById('wrapper'));
}
