import {React, useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import '/modals/App.css'
import Modal from "./modals/Modal";
import ModalNew from "./modals/ModalNew";
import axios from "axios";
import ExpertModal from "./modals/ExpertModal";


function Wrapper() {

    const [displayExpertModal, setDisplayExpertModal] = useState(false)

    const [openModal, setOpenModal] = useState(false)
    const [data, setData] = useState({userId: null,title:null})
    useEffect(() => {
        axios.get('https://jsonplaceholder.typicode.com/todos/1')
            .then((res)=>{
                console.log(res.data)
                // setDisplayExpertModal(true)
                setData({
                    userId: res.data.userId,
                    title: res.data.title
                })
            })
            .catch(err => console.log(err))
    },[])
    return (
        <div className="container">
            <div className="row">
                <div className="col">
                    { displayExpertModal && <ExpertModal/> }
                    {/*<button type="button" className="btn btn-primary"*/}
                    {/*        data-bs-toggle="modal" data-bs-target="#exampleModal">*/}
                    {/*    launch modal*/}
                    {/*</button>*/}
                    <ModalNew title={data.title} />
                    <p>Do you want to see this modal?</p>
                    <button className={'openModalBtn'} onClick={() => setOpenModal(true)}>Open Modal</button>
                    { openModal && <Modal closeModal={setOpenModal} userId={data.userId} title={data.title} /> }
                </div>
            </div>
        </div>
    );
}

export default Wrapper;

if (document.getElementById('wrapper')) {
    ReactDOM.render(<Wrapper />, document.getElementById('wrapper'));
}
