
import React from 'react'
import '/modals/modal.css'
import '/css/app.css'

export default function Modal({closeModal, userId, title}){
   return (
       <div className={'modalBackground'}>
            <div className="modalContainer jumbotron">
                <div className="titleCloseBtn">
                    <button onClick={() => closeModal(false)}> &times; </button>
                </div>
                <div className="title">
                    <p>Modal Title</p>
                </div>
                <hr/>
                <div className="body">
                    <p>Data obtained from fake rest api</p>
                    <div>
                        <h6>UserId: {userId}</h6>
                        <h6>Title: {title}</h6>
                    </div>
                </div>
                  <hr/>
                <div className="footer">
                    <button id={'cancelBtn'} onClick={() => closeModal(false)}>Cancel</button>
                    <button>Continue</button>
                </div>
            </div>
       </div>
   )
}

