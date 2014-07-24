db.b_logic.update({_cls:'Payment',status:'TO_BE_PROCESS_BY_BANK'},{$set:{'status':'EMI'}},{multi:true})
db.b_logic.update({_cls:'Payment',status:'PAID'},{$set:{'status':'ACP'}},{multi:true})
db.b_logic.update({_cls:'Payment',status:'DENIED'},{$set:{'status':'RCH'}},{multi:true})

