import {React, useState, useEffect} from 'react';
import ReactDOM from 'react-dom';
import '/modals/App.css'
//import Modal from "./modals/Modal";
import ModalNew from "./modals/ModalNew";
import axios from "axios";
import ExpertModal from "./modals/ExpertModal";
import FirstModal from "./modals/FirstModal";
import SecondModal from "./modals/SecondModal";
import ThirdModal from "./modals/ThirdModal";
import { Modal } from 'bootstrap'



function Wrapper() {
    useEffect(()=> {
       axios.get('https://jsonplaceholder.typicode.com/todos/1')
           .then(res => {
               console.log(res.data)
               createFirstModal()
           })
           .catch(err => {
               console.log(err)
           })
    }, [])
    const createFirstModal = () =>{
        let firstModal = new Modal(document.getElementById('firstModal'), {
            backdrop:'static',
            focus:true
        })
        firstModal.show()
    }
    const createSecondModal = () =>{
        let secondModal = new Modal(document.getElementById('secondModal'), {
            backdrop:'static',
            focus:true
        })
        secondModal.show()
    }

    //todo launch second modal from first modal
    return (
        <div className="container">
            <div className="row">
                <div className="col">
                   <h6>All the modals in this project goes here</h6>

                </div>
                <button className={'btn btn-warning'} onClick={()=> createFirstModal()}>Click to show modal</button>
            </div>
            <div className="row mt-5">
                <FirstModal launchSecondModal={createSecondModal}/>
                <SecondModal/>
                <ThirdModal/>
            </div>
        </div>
    );
}

export default Wrapper;

if (document.getElementById('wrapper')) {
    ReactDOM.render(<Wrapper />, document.getElementById('wrapper'));
}
