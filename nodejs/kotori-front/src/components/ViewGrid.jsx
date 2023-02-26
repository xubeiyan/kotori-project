function ViewFlexBox({ viewList }) {
  return <div className="flexbox-container">
    {viewList.length > 0 ? <div className="flexbox-item">
      Item
    </div> : <div className="flexbox-empty-item">目前没有图片</div>
    }
  </div>;
}

export default ViewFlexBox;
